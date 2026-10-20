<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ImportStagingModel;
use App\Models\MemberModel;
use App\Models\OrganizationModel;
use App\Models\ProgramModel;
use App\Models\PoliticalProfileModel;
use App\Models\ContributionModel;
use App\Models\DonationPreferenceModel;
use App\Models\FamilyMatchCandidateModel;
use App\Models\RegionUpaModel;
use App\Models\EducationModel;
use App\Models\WorkExperienceModel;
use App\Models\SkillModel;
use App\Models\JobFieldModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MigrateLegacyData extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:legacy-data';
    protected $description = 'Migrate legacy Excel questionnaire to Unified Person database';
    protected $usage       = 'migrate:legacy-data [path_to_xlsx]';
    protected $arguments   = [
        'path_to_xlsx' => 'The path to the legacy Excel questionnaire file'
    ];

    private $cleanWarnings = [];
    private $regionCache = [];

    public function run(array $params)
    {
        $filePath = $params[0] ?? null;

        if (empty($filePath)) {
            CLI::error("Please specify the path to the XLSX file.");
            CLI::write("Usage: php spark migrate:legacy-data _document_project/KUESIONER_ANGGOTA_PKS_PANMAS__Jawaban_.xlsx");
            return;
        }

        if (!file_exists($filePath)) {
            CLI::error("File not found at: " . $filePath);
            return;
        }

        CLI::write("Loading spreadsheet: " . $filePath, 'yellow');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Form Responses 1');

        if (!$sheet) {
            CLI::error("Sheet 'Form Responses 1' not found in the spreadsheet.");
            return;
        }

        $highestRow = $sheet->getHighestRow();
        CLI::write("Found " . ($highestRow - 1) . " rows to import.");

        $stagingModel = new ImportStagingModel();
        $memberModel = new MemberModel();

        // Load mappings
        $mappings = include ROOTPATH . 'scratch/column_mappings.php';

        CLI::write("------ TAHAP 1: Staging & Pembuatan Record Primary ------", 'blue');
        
        $insertedStagingCount = 0;
        $processedStagingCount = 0;
        $primaryCreatedCount = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            // Read raw row
            $rowData = [];
            $timestamp = '';
            $namaLengkap = '';

            foreach ($mappings as $m) {
                $colLetter = strtoupper($m['col']);
                $val = $sheet->getCell($colLetter . $row)->getValue();
                
                // Keep raw string representation
                if ($val instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                    $val = $val->getPlainText();
                }
                
                $dbColName = 'col_' . strtolower($colLetter);
                $rowData[$dbColName] = ($val !== null) ? (string)$val : '';

                if ($colLetter === 'A') $timestamp = (string)$val;
                if ($colLetter === 'B') $namaLengkap = (string)$val;
            }

            if (empty($namaLengkap)) {
                continue; // Skip empty rows
            }

            // Check if already in staging
            $existingStaging = $stagingModel->where('col_a', $timestamp)->where('col_b', $namaLengkap)->first();

            if (!$existingStaging) {
                $rowData['import_status'] = 'pending';
                $stagingId = $stagingModel->insert($rowData);
                $insertedStagingCount++;
                $stagingRow = $stagingModel->find($stagingId);
            } else {
                $stagingRow = $existingStaging;
            }

            // Process staging row if not already processed
            if ($stagingRow['import_status'] !== 'processed' || !$memberModel->find($stagingRow['created_member_id'])) {
                $memberId = $this->processStagingRow($stagingRow, $row);
                if ($memberId) {
                    $primaryCreatedCount++;
                    $processedStagingCount++;
                }
            } else {
                $processedStagingCount++;
            }
        }

        CLI::write("Staging: $insertedStagingCount new rows loaded, $processedStagingCount rows processed.");
        CLI::write("Primary Members created: $primaryCreatedCount");

        CLI::write("------ TAHAP 2: Spouse Matching ------", 'blue');
        $spouseStats = $this->processSpouseMatching();
        CLI::write("Spouse matching complete.");
        CLI::write(" - Spouse auto-linked (high confidence): " . $spouseStats['auto_linked']);
        CLI::write(" - Shadow Spouse created: " . $spouseStats['shadow_created']);
        CLI::write(" - Low-confidence candidates for review: " . $spouseStats['candidates_suggested']);

        CLI::write("------ TAHAP 3: Children Matching ------", 'blue');
        $childStats = $this->processChildrenMatching();
        CLI::write("Children matching complete.");
        CLI::write(" - Shadow Children created: " . $childStats['shadow_created']);
        CLI::write(" - Merged duplicate children: " . $childStats['merged_children']);

        CLI::write("------ RINGKASAN MIGRASI ------", 'green');
        CLI::write("Jumlah Baris Asal Excel   : " . ($highestRow - 1));
        CLI::write("Record Primary Dibuat     : " . $primaryCreatedCount);
        CLI::write("Spouse Auto-Linked        : " . $spouseStats['auto_linked']);
        CLI::write("Shadow Spouse Dibuat      : " . $spouseStats['shadow_created']);
        CLI::write("Shadow Children Dibuat    : " . $childStats['shadow_created']);
        CLI::write("Children Merged           : " . $childStats['merged_children']);
        CLI::write("Kandidat Review Pasangan  : " . $spouseStats['candidates_suggested']);
        
        if (!empty($this->cleanWarnings)) {
            CLI::write("Peringatan Data Cleaning (" . count($this->cleanWarnings) . " warning):", 'yellow');
            foreach (array_slice($this->cleanWarnings, 0, 20) as $w) {
                CLI::write(" - " . $w);
            }
            if (count($this->cleanWarnings) > 20) {
                CLI::write(" ... dan " . (count($this->cleanWarnings) - 20) . " peringatan lainnya (selengkapnya di log import_staging).");
            }
        }
    }

    private function processStagingRow($row, $excelRowNo)
    {
        $memberModel = new MemberModel();
        $stagingModel = new ImportStagingModel();

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Clean primary data fields
            $rt = $this->cleanRtRw($row['col_i']);
            $rw = $this->cleanRtRw($row['col_j']);
            
            // Standardize UPA Region
            $rawUpa = trim($row['col_f']);
            $kelurahanStr = $this->nullifyTrash($row['col_k']);
            $kecamatanStr = $this->nullifyTrash($row['col_l']);
            $kotaKabupatenStr = $this->nullifyTrash($row['col_m']);

            $regionIds = $this->resolveRegionIds($kelurahanStr, $kecamatanStr, $kotaKabupatenStr);

            if (!empty($rawUpa)) {
                $regionModel = new RegionUpaModel();
                $region = $regionModel->find($rawUpa);
                if (!$region) {
                    // Create regions_upa dynamically to satisfy FK constraint
                    $regionModel->insert([
                        'kode_upa' => $rawUpa,
                        'kelurahan_id' => $regionIds['village_id'],
                        'kecamatan_id' => $regionIds['district_id'],
                        'kota_kabupaten_id' => $regionIds['city_id']
                    ]);
                }
            }

            // Normalisasi Usia
            $usiaRaw = trim($row['col_d']);
            $usiaRange = $this->cleanUsiaRange($usiaRaw);

            // Clean status perkawinan
            $statusKawinRaw = trim($row['col_g']);
            $statusKawin = $this->cleanStatusPerkawinan($statusKawinRaw);

            // Clean status anggota
            $statusAnggotaRaw = trim($row['col_e']);
            $statusAnggota = $this->cleanStatusAnggota($statusAnggotaRaw);

            // Clean other text fields
            $namaLengkap = trim($row['col_b']);
            $alamatLengkap = $this->nullifyTrash($row['col_h']);
            $pekerjaan = $this->nullifyTrash($row['col_n']);
            $namaInstansi = $this->nullifyTrash($row['col_o']);
            $alamatKerja = $this->nullifyTrash($row['col_p']);
            $bidangPekerjaan = $this->nullifyTrash($row['col_q']);
            $keahlian = $this->nullifyTrash($row['col_s']);
            $pendapatanRange = $this->nullifyTrash($row['col_t']);
            $pendidikanTerakhir = $this->nullifyTrash($row['col_v']);

            $memberData = [
                'account_status' => 'invited',
                'spouse_pairing_status' => 'unmatched',
                'nama_lengkap' => $namaLengkap,
                'usia_range' => $usiaRange,
                'status_anggota' => $statusAnggota,
                'kode_upa' => !empty($rawUpa) ? $rawUpa : null,
                'status_perkawinan' => $statusKawin,
                'alamat_lengkap' => $alamatLengkap,
                'rt' => $rt,
                'rw' => $rw,
                'kelurahan_id' => $regionIds['village_id'],
                'kecamatan_id' => $regionIds['district_id'],
                'kota_kabupaten_id' => $regionIds['city_id'],
                'pekerjaan' => $pekerjaan,
                'nama_instansi' => $namaInstansi,
                'alamat_kerja' => $alamatKerja,
                'bidang_pekerjaan' => $bidangPekerjaan,
                'keahlian' => $keahlian,
                'pendapatan_range' => $pendapatanRange,
                'pendidikan_terakhir' => $pendidikanTerakhir,
                'is_active' => 1
            ];

            $memberId = $memberModel->insert($memberData);

            if (!$memberId) {
                throw new \Exception("Gagal insert member: " . json_encode($memberModel->errors()));
            }

            // Insert dynamic history tables
            // 1. Education
            if (!empty($pendidikanTerakhir)) {
                $jenjangRaw = trim($pendidikanTerakhir);
                $jenjang = 'Lainnya';
                if (stripos($jenjangRaw, 'sd') !== false) {
                    $jenjang = 'SD';
                } elseif (stripos($jenjangRaw, 'smp') !== false) {
                    $jenjang = 'SMP';
                } elseif (stripos($jenjangRaw, 'sma') !== false || stripos($jenjangRaw, 'smk') !== false || stripos($jenjangRaw, 'sederajat') !== false) {
                    $jenjang = 'SMA/SMK';
                } elseif (stripos($jenjangRaw, 'd3') !== false) {
                    $jenjang = 'D3';
                } elseif (stripos($jenjangRaw, 's1') !== false) {
                    $jenjang = 'S1';
                } elseif (stripos($jenjangRaw, 's2') !== false) {
                    $jenjang = 'S2';
                } elseif (stripos($jenjangRaw, 's3') !== false) {
                    $jenjang = 'S3';
                }
                
                $eduModel = new EducationModel();
                $eduModel->insert([
                    'member_id' => $memberId,
                    'jenjang' => $jenjang,
                    'nama_institusi' => 'Institusi Pendidikan',
                    'rentang_tahun' => '',
                ]);
            }

            // 2. Work Experience
            if (!empty($pekerjaan) || !empty($namaInstansi) || !empty($bidangPekerjaan) || !empty($pendapatanRange) || !empty($alamatKerja)) {
                $bidangId = 13; // default Lainnya
                if (!empty($bidangPekerjaan)) {
                    $bidangPekerjaanLower = strtolower(trim($bidangPekerjaan));
                    if (strpos($bidangPekerjaanLower, 'pendidikan') !== false || strpos($bidangPekerjaanLower, 'guru') !== false || strpos($bidangPekerjaanLower, 'dosen') !== false || strpos($bidangPekerjaanLower, 'pengajaran') !== false || strpos($bidangPekerjaanLower, 'mengajar') !== false || strpos($bidangPekerjaanLower, 'akademis') !== false) {
                        $bidangId = 3;
                    } elseif (strpos($bidangPekerjaanLower, 'it') !== false || strpos($bidangPekerjaanLower, 'teknologi') !== false || strpos($bidangPekerjaanLower, 'komputer') !== false || strpos($bidangPekerjaanLower, 'programmer') !== false || strpos($bidangPekerjaanLower, 'software') !== false || strpos($bidangPekerjaanLower, 'sistem info') !== false) {
                        $bidangId = 1;
                    } elseif (strpos($bidangPekerjaanLower, 'medis') !== false || strpos($bidangPekerjaanLower, 'kesehatan') !== false || strpos($bidangPekerjaanLower, 'dokter') !== false || strpos($bidangPekerjaanLower, 'perawat') !== false || strpos($bidangPekerjaanLower, 'bidan') !== false || strpos($bidangPekerjaanLower, 'apoteker') !== false || strpos($bidangPekerjaanLower, 'klinik') !== false || strpos($bidangPekerjaanLower, 'rs') !== false) {
                        $bidangId = 2;
                    } elseif (strpos($bidangPekerjaanLower, 'keuangan') !== false || strpos($bidangPekerjaanLower, 'bank') !== false || strpos($bidangPekerjaanLower, 'akuntan') !== false || strpos($bidangPekerjaanLower, 'pajak') !== false || strpos($bidangPekerjaanLower, 'finance') !== false) {
                        $bidangId = 4;
                    } elseif (strpos($bidangPekerjaanLower, 'dagang') !== false || strpos($bidangPekerjaanLower, 'wirausaha') !== false || strpos($bidangPekerjaanLower, 'retail') !== false || strpos($bidangPekerjaanLower, 'toko') !== false || strpos($bidangPekerjaanLower, 'online') !== false || strpos($bidangPekerjaanLower, 'sales') !== false || strpos($bidangPekerjaanLower, 'marketing') !== false || strpos($bidangPekerjaanLower, 'kuliner') !== false || strpos($bidangPekerjaanLower, 'makanan') !== false || strpos($bidangPekerjaanLower, 'warung') !== false || strpos($bidangPekerjaanLower, 'bisnis') !== false || strpos($bidangPekerjaanLower, 'jasa') !== false || strpos($bidangPekerjaanLower, 'usaha') !== false) {
                        $bidangId = 5;
                    } elseif (strpos($bidangPekerjaanLower, 'tani') !== false || strpos($bidangPekerjaanLower, 'peternak') !== false || strpos($bidangPekerjaanLower, 'kebun') !== false || strpos($bidangPekerjaanLower, 'pertanian') !== false || strpos($bidangPekerjaanLower, 'perkebunan') !== false || strpos($bidangPekerjaanLower, 'peternakan') !== false) {
                        $bidangId = 6;
                    } elseif (strpos($bidangPekerjaanLower, 'konstruksi') !== false || strpos($bidangPekerjaanLower, 'bangunan') !== false || strpos($bidangPekerjaanLower, 'properti') !== false || strpos($bidangPekerjaanLower, 'arsitek') !== false || strpos($bidangPekerjaanLower, 'sipil') !== false || strpos($bidangPekerjaanLower, 'real estate') !== false) {
                        $bidangId = 7;
                    } elseif (strpos($bidangPekerjaanLower, 'transportasi') !== false || strpos($bidangPekerjaanLower, 'logistik') !== false || strpos($bidangPekerjaanLower, 'driver') !== false || strpos($bidangPekerjaanLower, 'ojek') !== false || strpos($bidangPekerjaanLower, 'kurir') !== false || strpos($bidangPekerjaanLower, 'gojek') !== false || strpos($bidangPekerjaanLower, 'grab') !== false || strpos($bidangPekerjaanLower, 'pengiriman') !== false) {
                        $bidangId = 8;
                    } elseif (strpos($bidangPekerjaanLower, 'seni') !== false || strpos($bidangPekerjaanLower, 'desain') !== false || strpos($bidangPekerjaanLower, 'kreatif') !== false || strpos($bidangPekerjaanLower, 'media') !== false || strpos($bidangPekerjaanLower, 'editor') !== false || strpos($bidangPekerjaanLower, 'streaming') !== false || strpos($bidangPekerjaanLower, 'video') !== false || strpos($bidangPekerjaanLower, 'desainer') !== false || strpos($bidangPekerjaanLower, 'hiburan') !== false) {
                        $bidangId = 9;
                    } elseif (strpos($bidangPekerjaanLower, 'pns') !== false || strpos($bidangPekerjaanLower, 'pemerintah') !== false || strpos($bidangPekerjaanLower, 'tni') !== false || strpos($bidangPekerjaanLower, 'polri') !== false || strpos($bidangPekerjaanLower, 'militer') !== false || strpos($bidangPekerjaanLower, 'asn') !== false || strpos($bidangPekerjaanLower, 'publik') !== false || strpos($bidangPekerjaanLower, 'kelurahan') !== false || strpos($bidangPekerjaanLower, 'kecamatan') !== false) {
                        $bidangId = 10;
                    } elseif (strpos($bidangPekerjaanLower, 'hukum') !== false || strpos($bidangPekerjaanLower, 'legal') !== false || strpos($bidangPekerjaanLower, 'advokat') !== false || strpos($bidangPekerjaanLower, 'notaris') !== false || strpos($bidangPekerjaanLower, 'pengacara') !== false) {
                        $bidangId = 11;
                    } elseif (strpos($bidangPekerjaanLower, 'pabrik') !== false || strpos($bidangPekerjaanLower, 'manufaktur') !== false || strpos($bidangPekerjaanLower, 'industri') !== false || strpos($bidangPekerjaanLower, 'operator mesin') !== false || strpos($bidangPekerjaanLower, 'produksi') !== false) {
                        $bidangId = 12;
                    }
                }
                
                $workModel = new WorkExperienceModel();
                $workModel->insert([
                    'member_id' => $memberId,
                    'jabatan' => !empty($pekerjaan) ? $pekerjaan : 'Karyawan / Pegawai',
                    'nama_perusahaan' => !empty($namaInstansi) ? $namaInstansi : 'Tempat Kerja / Instansi',
                    'bidang_pekerjaan_id' => $bidangId,
                    'pendapatan_range' => $pendapatanRange ?? '',
                    'alamat_kerja' => $alamatKerja ?? '',
                    'rentang_tahun' => 'Saat Ini',
                ]);
            }

            // 3. Skills
            if (!empty($keahlian)) {
                $skillModel = new SkillModel();
                $skillModel->insert([
                    'member_id' => $memberId,
                    'nama_keahlian' => $keahlian,
                    'keterangan' => 'Keahlian Utama',
                ]);
            }

            // Insert M2M & Child tables
            
            // 1. member_organizations
            $orgModel = new OrganizationModel();
            $this->parseAndInsertOrg($row['col_cp'], $memberId, $orgModel);
            $this->parseAndInsertOrg($row['col_cq'], $memberId, $orgModel);

            // 2. member_program_interests
            $progModel = new ProgramModel();
            $this->parseAndInsertPrograms($row['col_cu'], $memberId, $progModel);

            // 3. member_political_profiles
            $polModel = new PoliticalProfileModel();
            $polModel->insert([
                'member_id' => $memberId,
                'status_pemilih_tetap' => $this->nullifyTrash($row['col_cr']),
                'frekuensi_berita_politik' => $this->nullifyTrash($row['col_cs']),
                'harapan_struktur' => $this->nullifyTrash($row['col_cv'])
            ]);

            // 4. member_contributions
            $contrModel = new ContributionModel();
            $bersedia = trim($row['col_cw']);
            $catatan = '';
            // If it contains conditional or explanation text, save as conditional
            if (mb_strlen($bersedia) > 15 || strpos(strtolower($bersedia), 'insya') !== false) {
                $catatan = $bersedia;
                $bersedia = 'Bersyarat';
            }
            $contrModel->insert([
                'member_id' => $memberId,
                'bersedia_sukseskan_program' => $bersedia,
                'catatan_kesediaan' => $catatan,
                'bidang_kontribusi' => $this->nullifyTrash($row['col_cx']),
                'jaringan_komunitas' => $this->nullifyTrash($row['col_cz'])
            ]);

            // 5. member_donation_preferences
            $donModel = new DonationPreferenceModel();
            $anonimRaw = strtolower(trim($row['col_de']));
            $anonim = ($anonimRaw === 'ya' || $anonimRaw === '1' || $anonimRaw === 'yes') ? 1 : 0;
            $donModel->insert([
                'member_id' => $memberId,
                'kesediaan_donasi' => $this->nullifyTrash($row['col_da']),
                'bentuk_donasi_lain' => $this->nullifyTrash($row['col_db']),
                'kepatuhan_iuran' => $this->nullifyTrash($row['col_dc']),
                'metode_donasi' => $this->nullifyTrash($row['col_dd']),
                'donasi_anonim' => $anonim,
                'kendala_iuran' => $this->nullifyTrash($row['col_ek'])
            ]);

            $db->transCommit();

            // Update staging status
            $stagingModel->update($row['id'], [
                'import_status' => 'processed',
                'created_member_id' => $memberId,
                'error_message' => null
            ]);

            return $memberId;

        } catch (\Exception $e) {
            $db->transRollback();
            $this->cleanWarnings[] = "Row {$excelRowNo} failed: " . $e->getMessage();
            $stagingModel->update($row['id'], [
                'import_status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function processSpouseMatching()
    {
        $memberModel = new MemberModel();
        $stagingModel = new ImportStagingModel();
        $candidateModel = new FamilyMatchCandidateModel();

        // Get all PRIMARY respondents who are married (have staging rows)
        $allStaging = $stagingModel->where('import_status', 'processed')->findAll();
        
        $shadowCreated = 0;
        $autoLinked = 0;
        $candidatesSuggested = 0;

        // Build a lookup: member_id => staging row
        $stagingByMemberId = [];
        foreach ($allStaging as $s) {
            if (!empty($s['created_member_id'])) {
                $stagingByMemberId[$s['created_member_id']] = $s;
            }
        }

        // Get all members who are married
        $marriedMembers = $memberModel->where('status_perkawinan', 'Menikah')->findAll();
        // Build lookup: member_id => member
        $memberById = [];
        foreach ($marriedMembers as $m) {
            $memberById[$m['id']] = $m;
        }

        // Only process PRIMARY respondents (those with staging)
        $primaryMarried = array_filter($marriedMembers, fn($m) => isset($stagingByMemberId[$m['id']]));

        // Keep local status tracking to avoid DB calls in inner loops
        $spousePairingStatus = [];
        $spouseMemberId = [];
        $jenisKelamin = [];
        foreach ($marriedMembers as $m) {
            $spousePairingStatus[$m['id']] = $m['spouse_pairing_status'];
            $spouseMemberId[$m['id']] = $m['spouse_member_id'];
            $jenisKelamin[$m['id']] = $m['jenis_kelamin'];
        }

        foreach ($primaryMarried as $a) {
            if (($spousePairingStatus[$a['id']] ?? null) === 'confirmed') continue;

            $staging = $stagingByMemberId[$a['id']];
            $namaPasanganRawA = trim($staging['col_c']);
            if (empty($namaPasanganRawA) || $namaPasanganRawA === '-') continue;

            $normSpouseA = $this->normalizeName($namaPasanganRawA);
            if (empty($normSpouseA)) continue;

            $bestCandidate = null;
            $bestScore = 0;

            // Search ALL married members (not just primary respondents) for a match
            foreach ($marriedMembers as $b) {
                if ($a['id'] === $b['id']) continue;
                // Skip already confirmed spouses of someone else
                if (($spousePairingStatus[$b['id']] ?? null) === 'confirmed') continue;

                $normMemberB = $this->normalizeName($b['nama_lengkap']);
                if (empty($normMemberB)) continue;

                // Direct name similarity: spouse name from A's form vs B's actual name
                $scoreAtoB = $this->nameSimilarity($namaPasanganRawA, $b['nama_lengkap']);

                // Cross-check: if B is also a primary respondent, check B's spouse name vs A's name
                $scoreBtoA = 0;
                if (isset($stagingByMemberId[$b['id']])) {
                    $namaPasanganRawB = trim($stagingByMemberId[$b['id']]['col_c']);
                    if (!empty($namaPasanganRawB) && $namaPasanganRawB !== '-') {
                        $scoreBtoA = $this->nameSimilarity($namaPasanganRawB, $a['nama_lengkap']);
                    }
                }

                // Use the best of: direct match, cross-check, or average if both available
                $nameScore = $scoreAtoB;
                if ($scoreBtoA > 0) {
                    $nameScore = max($scoreAtoB, $scoreBtoA, ($scoreAtoB + $scoreBtoA) / 2);
                }

                if ($nameScore < 65) continue;

                // Address bonus
                $addressBonus = 0;
                if (!empty($a['kelurahan_id']) && $a['kelurahan_id'] === $b['kelurahan_id']) $addressBonus += 5;
                if (!empty($a['rt']) && trim($a['rt']) === trim($b['rt']) && 
                    trim($a['rw']) === trim($b['rw']) && 
                    $a['kelurahan_id'] === $b['kelurahan_id']) $addressBonus += 5;

                $finalScore = min(100, $nameScore + $addressBonus);

                if ($finalScore > $bestScore) {
                    $bestScore = $finalScore;
                    $bestCandidate = [
                        'member_b_id' => $b['id'],
                        'final_score' => $finalScore,
                        'status' => $finalScore >= 90 ? 'suggested_high' : 'suggested_low'
                    ];
                }
            }

            if ($bestCandidate && $bestScore >= 80) {
                // HIGH CONFIDENCE: Auto-link bidirectionally
                $db = \Config\Database::connect();
                $db->transBegin();
                try {
                    $bId = $bestCandidate['member_b_id'];

                    // Infer genders from spouse name hints or children data
                    $genders = $this->inferGendersForPair($a['id'], $bId, $namaPasanganRawA, $stagingByMemberId, $jenisKelamin);

                    $memberModel->update($a['id'], [
                        'spouse_member_id' => $bId,
                        'spouse_pairing_status' => 'confirmed',
                        'jenis_kelamin' => $genders['a_gender']
                    ]);
                    $memberModel->update($bId, [
                        'spouse_member_id' => $a['id'],
                        'spouse_pairing_status' => 'confirmed',
                        'jenis_kelamin' => $genders['b_gender']
                    ]);

                    // Save candidate record for audit trail
                    $existing = $candidateModel
                        ->where('member_a_id', $a['id'])
                        ->where('member_b_id', $bId)
                        ->where('relation_type', 'spouse')
                        ->first();
                    if (!$existing) {
                        $candidateModel->insert([
                            'member_a_id' => $a['id'],
                            'member_b_id' => $bId,
                            'relation_type' => 'spouse',
                            'score' => $bestCandidate['final_score'],
                            'status' => 'confirmed'
                        ]);
                    }

                    // Update in-memory tracking
                    $spousePairingStatus[$a['id']] = 'confirmed';
                    $spouseMemberId[$a['id']] = $bId;
                    $jenisKelamin[$a['id']] = $genders['a_gender'];

                    $spousePairingStatus[$bId] = 'confirmed';
                    $spouseMemberId[$bId] = $a['id'];
                    $jenisKelamin[$bId] = $genders['b_gender'];

                    $autoLinked++;
                    $db->transCommit();
                } catch (\Exception $e) {
                    $db->transRollback();
                    $this->cleanWarnings[] = "Auto-link failed for {$a['nama_lengkap']}: " . $e->getMessage();
                }
            } elseif ($bestCandidate && $bestScore >= 65) {
                // LOW CONFIDENCE: Save as candidate for manual review
                $existing = $candidateModel
                    ->where('member_a_id', $a['id'])
                    ->where('member_b_id', $bestCandidate['member_b_id'])
                    ->where('relation_type', 'spouse')
                    ->first();
                if (!$existing) {
                    $candidateModel->insert([
                        'member_a_id' => $a['id'],
                        'member_b_id' => $bestCandidate['member_b_id'],
                        'relation_type' => 'spouse',
                        'score' => $bestCandidate['final_score'],
                        'status' => $bestCandidate['status']
                    ]);
                    $candidatesSuggested++;
                }
            } else {
                // NO MATCH FOUND: Create shadow profile for spouse
                $db = \Config\Database::connect();
                $db->transBegin();
                try {
                    $cleanSpouseName = trim(preg_replace('/\s*\((istri|suami|wife|husband)\)/i', '', $namaPasanganRawA));
                    
                    $genders = $this->inferGendersForPair($a['id'], null, $namaPasanganRawA, $stagingByMemberId, $jenisKelamin);

                    $shadowData = [
                        'account_status' => 'no_account',
                        'spouse_pairing_status' => 'confirmed',
                        'nama_lengkap' => $cleanSpouseName,
                        'jenis_kelamin' => $genders['b_gender'],
                        'status_anggota' => null,
                        'status_perkawinan' => 'Menikah',
                        'alamat_lengkap' => $a['alamat_lengkap'],
                        'rt' => $a['rt'],
                        'rw' => $a['rw'],
                        'kelurahan_id' => $a['kelurahan_id'],
                        'kecamatan_id' => $a['kecamatan_id'],
                        'kota_kabupaten_id' => $a['kota_kabupaten_id'],
                        'pekerjaan' => $this->nullifyTrash($staging['col_r']),
                        'pendapatan_range' => $this->nullifyTrash($staging['col_u']),
                        'spouse_member_id' => $a['id'],
                        'is_active' => 1
                    ];

                    $shadowId = $memberModel->insert($shadowData);
                    if ($shadowId) {
                        $memberModel->update($a['id'], [
                            'spouse_member_id' => $shadowId,
                            'spouse_pairing_status' => 'confirmed',
                            'jenis_kelamin' => $genders['a_gender']
                        ]);

                        // Update in-memory tracking for future iterations
                        $spousePairingStatus[$a['id']] = 'confirmed';
                        $spouseMemberId[$a['id']] = $shadowId;
                        $jenisKelamin[$a['id']] = $genders['a_gender'];

                        $spousePairingStatus[$shadowId] = 'confirmed';
                        $spouseMemberId[$shadowId] = $a['id'];
                        $jenisKelamin[$shadowId] = $genders['b_gender'];

                        // Also add shadow spouse to marriedMembers so they can be matched/found if needed
                        $newShadow = $shadowData;
                        $newShadow['id'] = $shadowId;
                        $marriedMembers[] = $newShadow;

                        $shadowCreated++;
                    }
                    $db->transCommit();
                } catch (\Exception $e) {
                    $db->transRollback();
                    $this->cleanWarnings[] = "Shadow spouse failed for {$a['nama_lengkap']}: " . $e->getMessage();
                }
            }
        }

        return [
            'shadow_created' => $shadowCreated,
            'auto_linked' => $autoLinked,
            'candidates_suggested' => $candidatesSuggested
        ];
    }

    /**
     * Infer genders for a pair based on spouse name hints and children data.
     * Returns ['a_gender' => 'L'|'P', 'b_gender' => 'L'|'P']
     */
    private function inferGendersForPair($aId, $bId, $namaPasanganRawA, $stagingByMemberId, &$jenisKelamin)
    {
        // 1. Check if spouse name has gender hint like (Istri) or (Suami)
        if (preg_match('/\(istri\)/i', $namaPasanganRawA)) {
            return ['a_gender' => 'L', 'b_gender' => 'P'];
        }
        if (preg_match('/\(suami\)/i', $namaPasanganRawA)) {
            return ['a_gender' => 'P', 'b_gender' => 'L'];
        }

        // 2. Check children's gender data to infer parent genders
        if (!empty($jenisKelamin[$aId])) {
            $oppositeGender = ($jenisKelamin[$aId] === 'L') ? 'P' : 'L';
            return ['a_gender' => $jenisKelamin[$aId], 'b_gender' => $oppositeGender];
        }

        if ($bId && !empty($jenisKelamin[$bId])) {
            $oppositeGender = ($jenisKelamin[$bId] === 'L') ? 'P' : 'L';
            return ['a_gender' => $oppositeGender, 'b_gender' => $jenisKelamin[$bId]];
        }

        // 3. Default fallback: assume primary respondent is male, spouse is female
        return ['a_gender' => 'L', 'b_gender' => 'P'];
    }

    private function processChildrenMatching()
    {
        $memberModel = new MemberModel();
        $stagingModel = new ImportStagingModel();
        
        $shadowCreated = 0;
        $mergedChildren = 0;

        // Get all processed staging rows
        $allStaging = $stagingModel->where('import_status', 'processed')->findAll();
        
        // Load all members into memory to optimize lookups
        $allMembers = $memberModel->findAll();
        $memberById = [];
        $memberByExactName = [];
        $childrenByFatherId = [];
        $childrenByMotherId = [];

        foreach ($allMembers as $m) {
            $memberById[$m['id']] = $m;
            $memberByExactName[strtolower($m['nama_lengkap'])] = $m;
            if ($m['father_member_id']) {
                $childrenByFatherId[$m['father_member_id']][$m['id']] = $m;
            }
            if ($m['mother_member_id']) {
                $childrenByMotherId[$m['mother_member_id']][$m['id']] = $m;
            }
        }

        // Track which member IDs have already had their children processed
        $processedParentIds = [];

        foreach ($allStaging as $staging) {
            $memberId = $staging['created_member_id'];
            if (empty($memberId) || isset($processedParentIds[$memberId])) continue;

            $member = $memberById[$memberId] ?? null;
            if (!$member) continue;

            // Get children from this respondent's staging data
            $children = $this->getChildrenFromStaging($memberId);
            if (empty($children)) {
                $processedParentIds[$memberId] = true;
                continue;
            }

            // Determine father/mother IDs
            $fatherId = null;
            $motherId = null;
            $spouseId = $member['spouse_member_id'] ?? null;

            if (($member['jenis_kelamin'] ?? null) === 'L') {
                $fatherId = $memberId;
                $motherId = $spouseId;
            } elseif (($member['jenis_kelamin'] ?? null) === 'P') {
                $motherId = $memberId;
                $fatherId = $spouseId;
            } else {
                // Gender unknown - default to father
                $fatherId = $memberId;
                $motherId = $spouseId;
            }

            // If spouse also has staging data, get their children list for deduplication
            $spouseChildren = [];
            $spouseStaging = null;
            if ($spouseId && !isset($processedParentIds[$spouseId])) {
                $spouseStaging = $stagingModel->where('created_member_id', $spouseId)->first();
                if ($spouseStaging) {
                    $spouseChildren = $this->getChildrenFromStaging($spouseId);
                }
            }

            // Process this parent's children
            foreach ($children as $childData) {
                // Check if this child already exists in members (e.g., created by spouse's earlier processing)
                $existingChild = $this->findExistingChildByName($childData, $fatherId, $motherId, $memberByExactName, $childrenByFatherId, $childrenByMotherId);
                
                if ($existingChild) {
                    // Update parent links if missing and merge properties
                    $updateData = [];
                    if (empty($existingChild['father_member_id']) && $fatherId) {
                        $updateData['father_member_id'] = $fatherId;
                    }
                    if (empty($existingChild['mother_member_id']) && $motherId) {
                        $updateData['mother_member_id'] = $motherId;
                    }
                    
                    // Merge more complete properties
                    if (strlen($childData['nama_lengkap']) > strlen($existingChild['nama_lengkap'])) {
                        $updateData['nama_lengkap'] = $childData['nama_lengkap'];
                    }
                    if (empty($existingChild['tanggal_lahir']) && !empty($childData['tanggal_lahir'])) {
                        $updateData['tanggal_lahir'] = $childData['tanggal_lahir'];
                    }
                    if (empty($existingChild['pendidikan_saat_ini']) && !empty($childData['pendidikan_saat_ini'])) {
                        $updateData['pendidikan_saat_ini'] = $childData['pendidikan_saat_ini'];
                    }
                    if (!empty($childData['sekolah_universitas']) && (empty($existingChild['sekolah_universitas']) || strlen($childData['sekolah_universitas']) > strlen($existingChild['sekolah_universitas']))) {
                        $updateData['sekolah_universitas'] = $childData['sekolah_universitas'];
                    }
                    if (!empty($childData['jurusan']) && (empty($existingChild['jurusan']) || strlen($childData['jurusan']) > strlen($existingChild['jurusan']))) {
                        $updateData['jurusan'] = $childData['jurusan'];
                    }
                    if (empty($existingChild['judul_skripsi']) && !empty($childData['judul_skripsi'])) {
                        $updateData['judul_skripsi'] = $childData['judul_skripsi'];
                    }
                    if (empty($existingChild['status_kerja']) && !empty($childData['status_kerja'])) {
                        $updateData['status_kerja'] = $childData['status_kerja'];
                    }
                    if (!empty($childData['pekerjaan']) && (empty($existingChild['pekerjaan']) || strlen($childData['pekerjaan']) > strlen($existingChild['pekerjaan']))) {
                        $updateData['pekerjaan'] = $childData['pekerjaan'];
                    }
                    if (!empty($childData['hobi']) && (empty($existingChild['hobi']) || strlen($childData['hobi']) > strlen($existingChild['hobi']))) {
                        $updateData['hobi'] = $childData['hobi'];
                    }
                    if (!empty($childData['keahlian']) && (empty($existingChild['keahlian']) || strlen($childData['keahlian']) > strlen($existingChild['keahlian']))) {
                        $updateData['keahlian'] = $childData['keahlian'];
                    }

                    if (!empty($updateData)) {
                        $memberModel->update($existingChild['id'], $updateData);

                        // Update in-memory structures
                        foreach ($updateData as $key => $val) {
                            $existingChild[$key] = $val;
                        }
                        $memberById[$existingChild['id']] = $existingChild;
                        $memberByExactName[strtolower($existingChild['nama_lengkap'])] = $existingChild;
                        if ($fatherId) {
                            $childrenByFatherId[$fatherId][$existingChild['id']] = $existingChild;
                        }
                        if ($motherId) {
                            $childrenByMotherId[$motherId][$existingChild['id']] = $existingChild;
                        }
                    }
                    $mergedChildren++;
                    continue;
                }

                // Create new shadow child profile with BOTH parent links
                $db = \Config\Database::connect();
                $db->transBegin();
                try {
                    $shadowData = $this->buildShadowChildData($childData, $fatherId, $motherId, $member);
                    $childId = $memberModel->insert($shadowData);
                    if ($childId) {
                        $shadowCreated++;

                        // Register the new child in our in-memory lookups so they can be matched/merged later
                        $newChild = $shadowData;
                        $newChild['id'] = $childId;

                        $memberById[$childId] = $newChild;
                        $memberByExactName[strtolower($newChild['nama_lengkap'])] = $newChild;
                        if ($fatherId) {
                            $childrenByFatherId[$fatherId][$childId] = $newChild;
                        }
                        if ($motherId) {
                            $childrenByMotherId[$motherId][$childId] = $newChild;
                        }
                    }
                    $db->transCommit();
                } catch (\Exception $e) {
                    $db->transRollback();
                    $this->cleanWarnings[] = "Child creation failed for '{$childData['nama_lengkap']}' (parent: {$member['nama_lengkap']}): " . $e->getMessage();
                }
            }

            $processedParentIds[$memberId] = true;

            // Also mark spouse as processed so we don't create duplicates
            if ($spouseId && $spouseStaging) {
                // Process any children that the spouse listed but this respondent did NOT
                foreach ($spouseChildren as $spouseChild) {
                    // Check if this child was already created from the primary respondent
                    $existingChild = $this->findExistingChildByName($spouseChild, $fatherId, $motherId, $memberByExactName, $childrenByFatherId, $childrenByMotherId);
                    
                    if ($existingChild) {
                        // Ensure both parent links are set and merge properties
                        $updateData = [];
                        if (empty($existingChild['father_member_id']) && $fatherId) {
                            $updateData['father_member_id'] = $fatherId;
                        }
                        if (empty($existingChild['mother_member_id']) && $motherId) {
                            $updateData['mother_member_id'] = $motherId;
                        }
                        
                        // Merge more complete properties
                        if (strlen($spouseChild['nama_lengkap']) > strlen($existingChild['nama_lengkap'])) {
                            $updateData['nama_lengkap'] = $spouseChild['nama_lengkap'];
                        }
                        if (empty($existingChild['tanggal_lahir']) && !empty($spouseChild['tanggal_lahir'])) {
                            $updateData['tanggal_lahir'] = $spouseChild['tanggal_lahir'];
                        }
                        if (empty($existingChild['pendidikan_saat_ini']) && !empty($spouseChild['pendidikan_saat_ini'])) {
                            $updateData['pendidikan_saat_ini'] = $spouseChild['pendidikan_saat_ini'];
                        }
                        if (!empty($spouseChild['sekolah_universitas']) && (empty($existingChild['sekolah_universitas']) || strlen($spouseChild['sekolah_universitas']) > strlen($existingChild['sekolah_universitas']))) {
                            $updateData['sekolah_universitas'] = $spouseChild['sekolah_universitas'];
                        }
                        if (!empty($spouseChild['jurusan']) && (empty($existingChild['jurusan']) || strlen($spouseChild['jurusan']) > strlen($existingChild['jurusan']))) {
                            $updateData['jurusan'] = $spouseChild['jurusan'];
                        }
                        if (empty($existingChild['judul_skripsi']) && !empty($spouseChild['judul_skripsi'])) {
                            $updateData['judul_skripsi'] = $spouseChild['judul_skripsi'];
                        }
                        if (empty($existingChild['status_kerja']) && !empty($spouseChild['status_kerja'])) {
                            $updateData['status_kerja'] = $spouseChild['status_kerja'];
                        }
                        if (!empty($spouseChild['pekerjaan']) && (empty($existingChild['pekerjaan']) || strlen($spouseChild['pekerjaan']) > strlen($existingChild['pekerjaan']))) {
                            $updateData['pekerjaan'] = $spouseChild['pekerjaan'];
                        }
                        if (!empty($spouseChild['hobi']) && (empty($existingChild['hobi']) || strlen($spouseChild['hobi']) > strlen($existingChild['hobi']))) {
                            $updateData['hobi'] = $spouseChild['hobi'];
                        }
                        if (!empty($spouseChild['keahlian']) && (empty($existingChild['keahlian']) || strlen($spouseChild['keahlian']) > strlen($existingChild['keahlian']))) {
                            $updateData['keahlian'] = $spouseChild['keahlian'];
                        }

                        if (!empty($updateData)) {
                            $memberModel->update($existingChild['id'], $updateData);

                            // Update in-memory structures
                            foreach ($updateData as $key => $val) {
                                $existingChild[$key] = $val;
                            }
                            $memberById[$existingChild['id']] = $existingChild;
                            $memberByExactName[strtolower($existingChild['nama_lengkap'])] = $existingChild;
                            if ($fatherId) {
                                $childrenByFatherId[$fatherId][$existingChild['id']] = $existingChild;
                            }
                            if ($motherId) {
                                $childrenByMotherId[$motherId][$existingChild['id']] = $existingChild;
                            }
                        }
                        $mergedChildren++;
                    } else {
                        // New child only listed by spouse, create it
                        $db = \Config\Database::connect();
                        $db->transBegin();
                        try {
                            $spouse = $memberById[$spouseId] ?? null;
                            $shadowData = $this->buildShadowChildData($spouseChild, $fatherId, $motherId, $spouse ?: $member);
                            $childId = $memberModel->insert($shadowData);
                            if ($childId) {
                                $shadowCreated++;

                                // Register the new child in our in-memory lookups so they can be matched/merged later
                                $newChild = $shadowData;
                                $newChild['id'] = $childId;

                                $memberById[$childId] = $newChild;
                                $memberByExactName[strtolower($newChild['nama_lengkap'])] = $newChild;
                                if ($fatherId) {
                                    $childrenByFatherId[$fatherId][$childId] = $newChild;
                                }
                                if ($motherId) {
                                    $childrenByMotherId[$motherId][$childId] = $newChild;
                                }
                            }
                            $db->transCommit();
                        } catch (\Exception $e) {
                            $db->transRollback();
                            $this->cleanWarnings[] = "Spouse child creation failed for '{$spouseChild['nama_lengkap']}': " . $e->getMessage();
                        }
                    }
                }
                $processedParentIds[$spouseId] = true;
            }
        }

        return [
            'shadow_created' => $shadowCreated,
            'merged_children' => $mergedChildren
        ];
    }

    /**
     * Find an existing child member by name, searching for children of given parents.
     */
    private function findExistingChildByName($childData, $fatherId, $motherId, &$memberByExactName, &$childrenByFatherId, &$childrenByMotherId)
    {
        $childName = $childData['nama_lengkap'];
        $lowerName = strtolower($childName);
        if (isset($memberByExactName[$lowerName])) {
            return $memberByExactName[$lowerName];
        }

        // Then try normalized name comparison among children of these parents
        $candidates = [];
        if ($fatherId && isset($childrenByFatherId[$fatherId])) {
            $candidates = array_merge($candidates, array_values($childrenByFatherId[$fatherId]));
        }
        if ($motherId && isset($childrenByMotherId[$motherId])) {
            $candidates = array_merge($candidates, array_values($childrenByMotherId[$motherId]));
        }

        foreach ($candidates as $c) {
            if ($this->areChildrenSimilar($childData, $c)) {
                return $c;
            }
        }

        return null;
    }

    /**
     * Normalize child name by replacing common abbreviations.
     */
    private function normalizeNameForChild(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/[.,]/', '', $name);
        $name = preg_replace('/\bmuhammad\b|\bmuhamad\b|\bmuh\b|\bm\b/', 'm', $name);
        $name = preg_replace('/\babd\b|\babdul\b|\bعبد\b/', 'abdu', $name);
        return $name;
    }

    /**
     * Smart similarity helper for children of the same parents.
     */
    private function areChildrenSimilar($childA, $childB)
    {
        if ($childA['jenis_kelamin'] !== $childB['jenis_kelamin']) {
            return false;
        }

        $nameA = $this->normalizeNameForChild($childA['nama_lengkap']);
        $nameB = $this->normalizeNameForChild($childB['nama_lengkap']);

        if ($nameA === $nameB) {
            return true;
        }

        // Substring checks
        if (strlen($nameA) >= 4 && strlen($nameB) >= 4) {
            if (strpos($nameA, $nameB) !== false || strpos($nameB, $nameA) !== false) {
                return true;
            }
        }

        // Distinct word check
        $wordsA = array_filter(explode(' ', $nameA), fn($w) => strlen($w) > 2);
        $wordsB = array_filter(explode(' ', $nameB), fn($w) => strlen($w) > 2);
        $intersect = array_intersect($wordsA, $wordsB);

        $hasCommonDistinctWord = false;
        foreach ($intersect as $word) {
            if (strlen($word) >= 4 && !in_array($word, ['anak', 'putra', 'putri'])) {
                $hasCommonDistinctWord = true;
                break;
            }
        }

        if ($hasCommonDistinctWord) {
            // Check secondary attributes to confirm
            if (!empty($childA['tanggal_lahir']) && !empty($childB['tanggal_lahir'])) {
                if ($childA['tanggal_lahir'] === $childB['tanggal_lahir']) {
                    return true;
                }
                $yearA = date('Y', strtotime($childA['tanggal_lahir']));
                $yearB = date('Y', strtotime($childB['tanggal_lahir']));
                if ($yearA === $yearB) {
                    return true;
                }
            }

            $pendA = strtolower(trim($childA['pendidikan_saat_ini'] ?? ''));
            $pendB = strtolower(trim($childB['pendidikan_saat_ini'] ?? ''));
            if (!empty($pendA) && !empty($pendB) && $pendA === $pendB) {
                return true;
            }

            $schA = strtolower(trim($childA['sekolah_universitas'] ?? ''));
            $schB = strtolower(trim($childB['sekolah_universitas'] ?? ''));
            if (!empty($schA) && !empty($schB)) {
                if ($schA === $schB || strpos($schA, $schB) !== false || strpos($schB, $schA) !== false) {
                    return true;
                }
            }
        }

        // Levenshtein similarity fallback
        if ($this->nameSimilarity($childA['nama_lengkap'], $childB['nama_lengkap']) >= 80) {
            return true;
        }

        return false;
    }

    private function buildShadowChildData($child, $fatherId, $motherId, $parentMember)
    {
        return [
            'account_status' => 'no_account',
            'spouse_pairing_status' => 'no_pair',
            'nama_lengkap' => $child['nama_lengkap'],
            'jenis_kelamin' => $child['jenis_kelamin'],
            'tanggal_lahir' => $child['tanggal_lahir'],
            'status_anggota' => null,
            'status_perkawinan' => $child['status_pernikahan'] ?: 'Lajang',
            'father_member_id' => $fatherId,
            'mother_member_id' => $motherId,
            'alamat_lengkap' => $parentMember['alamat_lengkap'] ?? null,
            'rt' => $parentMember['rt'] ?? null,
            'rw' => $parentMember['rw'] ?? null,
            'kelurahan_id' => $parentMember['kelurahan_id'] ?? null,
            'kecamatan_id' => $parentMember['kecamatan_id'] ?? null,
            'kota_kabupaten_id' => $parentMember['kota_kabupaten_id'] ?? null,
            'pendidikan_saat_ini' => $child['pendidikan_saat_ini'] ?? null,
            'mengikuti_mentoring' => (isset($child['mengikuti_mentoring']) && strtolower($child['mengikuti_mentoring']) === 'sudah') ? 1 : 0,
            'sekolah_universitas' => $child['sekolah_universitas'] ?? null,
            'jurusan' => $child['jurusan'] ?? null,
            'judul_skripsi' => $child['judul_skripsi'] ?? null,
            'status_kerja' => $child['status_kerja'] ?? null,
            'pekerjaan' => $child['pekerjaan'] ?? null,
            'hobi' => $child['hobi'] ?? null,
            'keahlian' => $child['keahlian'] ?? null,
            'is_active' => 1
        ];
    }

    private function findPrimaryChildMatch($child)
    {
        $memberModel = new MemberModel();
        $candidates = $memberModel
            ->where('status_anggota IS NOT NULL')
            ->where('jenis_kelamin', $child['jenis_kelamin'])
            ->findAll();

        foreach ($candidates as $c) {
            $nameSim = $this->nameSimilarity($child['nama_lengkap'], $c['nama_lengkap']);
            if ($nameSim >= 85) {
                // If DOB is also identical or similar
                if ($child['tanggal_lahir'] && $c['tanggal_lahir']) {
                    if ($child['tanggal_lahir'] === $c['tanggal_lahir']) {
                        return $c;
                    }
                } else {
                    return $c; // Match just by name if DOB is missing
                }
            }
        }

        return null;
    }

    private function getChildrenFromStaging($memberId)
    {
        $stagingModel = new ImportStagingModel();
        $row = $stagingModel->where('created_member_id', $memberId)->first();
        if (!$row) return [];

        $children = [];
        
        // Col indices mapping for Anak 1 to 10
        // We defined this manually based on mappings
        $anakMappings = [
            1  => ['nama' => 'col_x',  'jk' => 'col_y',  'dob' => 'col_z',  'nikah' => 'col_aa', 'pend' => 'col_ab', 'ment' => 'col_ac', 'univ' => 'col_df', 'jur' => 'col_dg', 'skripsi' => 'col_di', 'status_kerja' => 'col_dh', 'pekerjaan' => 'col_en', 'hobi' => 'col_el', 'keahlian' => 'col_em'],
            2  => ['nama' => 'col_ae', 'jk' => 'col_af', 'dob' => 'col_ag', 'nikah' => 'col_ah', 'pend' => 'col_ai', 'ment' => 'col_aj', 'univ' => 'col_dj', 'jur' => 'col_dl', 'skripsi' => 'col_dk', 'status_kerja' => 'col_eo', 'pekerjaan' => 'col_ep', 'hobi' => 'col_eq', 'keahlian' => 'col_er'],
            3  => ['nama' => 'col_al', 'jk' => 'col_am', 'dob' => 'col_an', 'nikah' => 'col_ao', 'pend' => 'col_ap', 'ment' => 'col_aq', 'univ' => 'col_dm', 'jur' => 'col_dn', 'skripsi' => 'col_do', 'status_kerja' => 'col_es', 'pekerjaan' => 'col_et', 'hobi' => 'col_eu', 'keahlian' => 'col_ev'],
            4  => ['nama' => 'col_as', 'jk' => 'col_at', 'dob' => 'col_au', 'nikah' => 'col_av', 'pend' => 'col_aw', 'ment' => 'col_ax', 'univ' => 'col_dp', 'jur' => 'col_dr', 'skripsi' => 'col_dq', 'status_kerja' => 'col_ew', 'pekerjaan' => 'col_ex', 'hobi' => 'col_ey', 'keahlian' => 'col_ez'],
            5  => ['nama' => 'col_az', 'jk' => 'col_ba', 'dob' => 'col_bb', 'nikah' => 'col_bc', 'pend' => 'col_bd', 'ment' => 'col_be', 'univ' => 'col_ds', 'jur' => 'col_dt', 'skripsi' => 'col_du', 'status_kerja' => 'col_fa', 'pekerjaan' => 'col_fb', 'hobi' => 'col_fc', 'keahlian' => 'col_fd'],
            6  => ['nama' => 'col_bg', 'jk' => 'col_bh', 'dob' => 'col_bi', 'nikah' => 'col_bj', 'pend' => 'col_bk', 'ment' => 'col_bl', 'univ' => 'col_dv', 'jur' => 'col_dw', 'skripsi' => 'col_dx', 'status_kerja' => 'col_fe', 'pekerjaan' => 'col_ff', 'hobi' => 'col_fg', 'keahlian' => 'col_fh'],
            7  => ['nama' => 'col_bn', 'jk' => 'col_bo', 'dob' => 'col_bp', 'nikah' => 'col_bq', 'pend' => 'col_br', 'ment' => 'col_bs', 'univ' => 'col_dy', 'jur' => 'col_dz', 'skripsi' => 'col_ea', 'status_kerja' => 'col_fi', 'pekerjaan' => 'col_fj', 'hobi' => 'col_fk', 'keahlian' => 'col_fl'],
            8  => ['nama' => 'col_bu', 'jk' => 'col_bv', 'dob' => 'col_bw', 'nikah' => 'col_bx', 'pend' => 'col_by', 'ment' => 'col_bz', 'univ' => 'col_eb', 'jur' => 'col_ec', 'skripsi' => 'col_ed', 'status_kerja' => 'col_fm', 'pekerjaan' => 'col_fn', 'hobi' => 'col_fo', 'keahlian' => 'col_fp'],
            9  => ['nama' => 'col_cb', 'jk' => 'col_cc', 'dob' => 'col_cd', 'nikah' => 'col_ce', 'pend' => 'col_cf', 'ment' => 'col_cg', 'univ' => 'col_ee', 'jur' => 'col_ef', 'skripsi' => 'col_eg', 'status_kerja' => 'col_fq', 'pekerjaan' => 'col_fr', 'hobi' => 'col_fs', 'keahlian' => 'col_ft'],
            10 => ['nama' => 'col_ci', 'jk' => 'col_cj', 'dob' => 'col_ck', 'nikah' => 'col_cl', 'pend' => 'col_cm', 'ment' => 'col_cn', 'univ' => 'col_eh', 'jur' => 'col_ei', 'skripsi' => 'col_ej', 'status_kerja' => 'col_fu', 'pekerjaan' => 'col_fv', 'hobi' => 'col_fw', 'keahlian' => 'col_fx'],
        ];

        foreach ($anakMappings as $num => $cols) {
            $nama = trim($row[$cols['nama']] ?? '');
            if (!empty($nama) && $nama !== '-') {
                // Map raw gender
                $jkRaw = strtolower(trim($row[$cols['jk']] ?? ''));
                $jk = ($jkRaw === 'perempuan' || $jkRaw === 'p') ? 'P' : 'L';

                $children[] = [
                    'nama_lengkap' => $nama,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $this->cleanDate($row[$cols['dob']]),
                    'status_pernikahan' => $this->nullifyTrash($row[$cols['nikah']]),
                    'pendidikan_saat_ini' => $this->nullifyTrash($row[$cols['pend']]),
                    'mengikuti_mentoring' => $this->nullifyTrash($row[$cols['ment']]),
                    'sekolah_universitas' => $this->nullifyTrash($row[$cols['univ']]),
                    'jurusan' => $this->nullifyTrash($row[$cols['jur']]),
                    'judul_skripsi' => $this->nullifyTrash($row[$cols['skripsi']]),
                    'status_kerja' => $this->nullifyTrash($row[$cols['status_kerja']]),
                    'pekerjaan' => $this->nullifyTrash($row[$cols['pekerjaan']]),
                    'hobi' => $this->nullifyTrash($row[$cols['hobi']]),
                    'keahlian' => $this->nullifyTrash($row[$cols['keahlian']]),
                ];
            }
        }

        return $children;
    }

    private function parseAndInsertOrg($val, $memberId, $orgModel)
    {
        $val = trim($val);
        if (empty($val) || $val === '-') return;

        // format: Org 1 : ketua, Org 2 : anggota
        $entries = explode(',', $val);
        foreach ($entries as $entry) {
            $parts = explode(':', $entry);
            $namaOrg = trim($parts[0] ?? '');
            $jabatan = trim($parts[1] ?? 'Anggota');
            
            if (!empty($namaOrg)) {
                $orgModel->insert([
                    'member_id' => $memberId,
                    'nama_organisasi' => $namaOrg,
                    'jabatan' => $jabatan
                ]);
            }
        }
    }

    private function parseAndInsertPrograms($val, $memberId, $progModel)
    {
        $val = trim($val);
        if (empty($val) || $val === '-') return;

        $choices = array_map('trim', explode(',', $val));
        $db = \Config\Database::connect();

        foreach ($choices as $choice) {
            if (empty($choice)) continue;

            // Find or create program
            $prog = $progModel->where('nama_program', $choice)->first();
            if (!$prog) {
                $progId = $progModel->insert([
                    'nama_program' => $choice,
                    'is_active' => 1
                ]);
            } else {
                $progId = $prog['id'];
            }

            // Link in many to many
            $db->table('member_program_interests')->insert([
                'member_id' => $memberId,
                'program_id' => $progId
            ]);
        }
    }

    // Fuzzy matching algorithms
    private function normalizeName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/\s+/', ' ', $name);
        $name = preg_replace('/[.,]/', '', $name);
        $titles = ['hj', 'h', 'dr', 'drs', 'dra', 'ir', 'st', 'se', 'spd', 'msi', 'mm'];
        $parts = array_filter(explode(' ', $name), fn($p) => !in_array($p, $titles));
        return implode(' ', $parts);
    }

    private function nameSimilarity(string $a, string $b): float
    {
        $a = $this->normalizeName($a);
        $b = $this->normalizeName($b);
        if ($a === '' || $b === '') return 0.0;
        if ($a === $b) return 100.0;

        similar_text($a, $b, $percentSimilar);

        $maxLen = max(strlen($a), strlen($b));
        $levScore = $maxLen > 0 ? (1 - levenshtein($a, $b) / $maxLen) * 100 : 0;

        return max($percentSimilar, $levScore);
    }

    private function evaluateSpouseCandidate($a, $b, string $namaPasanganRawA, string $namaPasanganRawB): ?array
    {
        $scoreAtoB = $this->nameSimilarity($namaPasanganRawA, $b['nama_lengkap']);
        $scoreBtoA = $this->nameSimilarity($namaPasanganRawB, $a['nama_lengkap']);
        $nameScore = ($scoreAtoB + $scoreBtoA) / 2;

        if ($nameScore < 70) {
            return null;
        }

        $addressBonus = 0;
        if (!empty($a['kelurahan_id']) && $a['kelurahan_id'] === $b['kelurahan_id']) $addressBonus += 10;
        if (trim($a['rt']) === trim($b['rt']) && trim($a['rw']) === trim($b['rw'])
            && !empty($a['kelurahan_id']) && $a['kelurahan_id'] === $b['kelurahan_id'] && $a['rt'] !== '') $addressBonus += 10;

        $finalScore = min(100, $nameScore + $addressBonus);

        return [
            'final_score' => $finalScore,
            'status' => $finalScore >= 90 ? 'suggested_high' : 'suggested_low',
            'member_b_id' => $b['id']
        ];
    }

    private function evaluateChildCandidate(array $childA, array $childB): ?array
    {
        if ($childA['jenis_kelamin'] !== $childB['jenis_kelamin']) {
            return null;
        }

        $dobA = $childA['tanggal_lahir'];
        $dobB = $childB['tanggal_lahir'];
        $dobScore = 0;
        if ($dobA && $dobB) {
            if ($dobA === $dobB) {
                $dobScore = 50;
            } else {
                $diffDays = abs(strtotime($dobA) - strtotime($dobB)) / 86400;
                if ($diffDays <= 31) $dobScore = 25;
            }
        }

        $nameScore = $this->nameSimilarity($childA['nama_lengkap'], $childB['nama_lengkap']);
        $finalScore = ($nameScore * 0.5) + $dobScore;

        if ($finalScore < 60) {
            return null;
        }

        return [
            'final_score' => $finalScore,
            'status' => ($dobA === $dobB && $nameScore >= 85) ? 'suggested_high' : 'suggested_low',
        ];
    }

    private function resolveRegionIds($kelurahanStr, $kecamatanStr, $kotaKabupatenStr)
    {
        $kel = strtoupper(trim($kelurahanStr ?? ''));
        $kec = strtoupper(trim($kecamatanStr ?? ''));
        $city = strtoupper(trim($kotaKabupatenStr ?? ''));

        if (empty($kel) && empty($kec) && empty($city)) {
            return ['city_id' => null, 'district_id' => null, 'village_id' => null];
        }

        $cacheKey = "{$kel}|{$kec}|{$city}";
        if (isset($this->regionCache[$cacheKey])) {
            return $this->regionCache[$cacheKey];
        }

        $db = \Config\Database::connect();

        // 1. Resolve City
        $cityClean = $city;
        if (strpos($cityClean, 'KOTA') === false && strpos($cityClean, 'KABUPATEN') === false && !empty($cityClean)) {
            $cityClean = "%" . $cityClean . "%";
        }
        
        $cityRow = null;
        if (!empty($city)) {
            $cityRow = $db->table('cms_region02_cities')
                ->like('name', $cityClean)
                ->get()
                ->getRowArray();
        }

        // Fallback for Depok
        if (!$cityRow && strpos($city, 'DEPOK') !== false) {
            $cityRow = $db->table('cms_region02_cities')
                ->where('city_id', '3276')
                ->get()
                ->getRowArray();
        }

        if (!$cityRow) {
            $result = ['city_id' => null, 'district_id' => null, 'village_id' => null];
            $this->regionCache[$cacheKey] = $result;
            return $result;
        }

        $cityDbId = $cityRow['id'];
        $cityCode = $cityRow['city_id'];

        // 2. Resolve District (Kecamatan)
        $distRow = null;
        if (!empty($kec)) {
            $kecNoSpace = str_replace(' ', '', $kec);
            $distRow = $db->table('cms_region03_districts')
                ->where('city_id', $cityCode)
                ->groupStart()
                    ->where('name', $kec)
                    ->orWhere("REPLACE(name, ' ', '') = ", $kecNoSpace)
                ->groupEnd()
                ->get()
                ->getRowArray();

            if (!$distRow) {
                $distRow = $db->table('cms_region03_districts')
                    ->where('city_id', $cityCode)
                    ->like('name', $kec)
                    ->get()
                    ->getRowArray();
            }
        }

        if (!$distRow) {
            $result = ['city_id' => $cityDbId, 'district_id' => null, 'village_id' => null];
            $this->regionCache[$cacheKey] = $result;
            return $result;
        }

        $distDbId = $distRow['id'];
        $distCode = $distRow['district_id'];

        // 3. Resolve Village (Kelurahan)
        $villRow = null;
        if (!empty($kel)) {
            $kelNoSpace = str_replace(' ', '', $kel);
            $villRow = $db->table('cms_region04_villages')
                ->where('district_id', $distCode)
                ->groupStart()
                    ->where('name', $kel)
                    ->orWhere("REPLACE(name, ' ', '') = ", $kelNoSpace)
                ->groupEnd()
                ->get()
                ->getRowArray();

            if (!$villRow) {
                $villRow = $db->table('cms_region04_villages')
                    ->where('district_id', $distCode)
                    ->like('name', $kel)
                    ->get()
                    ->getRowArray();
            }
        }

        $villDbId = $villRow ? $villRow['id'] : null;

        $result = [
            'city_id' => $cityDbId,
            'district_id' => $distDbId,
            'village_id' => $villDbId
        ];
        $this->regionCache[$cacheKey] = $result;
        return $result;
    }

    // Data cleaning utilities
    private function cleanRtRw($value)
    {
        $value = trim($value);
        if ($value === '' || $value === '-') return null;
        if (preg_match('/^\d+\.0$/', $value)) {
            $value = (string)intval($value);
        }
        $value = preg_replace('/[^\d\/]/', '', $value);
        if ($value === '') return null;
        return $value;
    }

    private function cleanDate($value)
    {
        if (empty($value) || $value === '-') return null;
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        $value = trim($value);
        $time = strtotime(str_replace('/', '-', $value));
        if ($time !== false) {
            return date('Y-m-d', $time);
        }
        return null;
    }

    private function cleanUsiaRange($value)
    {
        $value = trim($value);
        if (empty($value) || $value === '-') return null;

        // If it's a number, map to standard range
        if (is_numeric($value)) {
            $age = (int)$value;
            if ($age >= 18 && $age <= 25) return '18-25';
            if ($age >= 26 && $age <= 35) return '26-35';
            if ($age >= 36 && $age <= 45) return '36-45';
            if ($age >= 46 && $age <= 55) return '46-55';
            if ($age >= 56 && $age <= 60) return '55-60';
            if ($age > 60) return 'di atas 60 tahun';
        }

        // Just clean strings
        return $value;
    }

    private function cleanStatusPerkawinan($value)
    {
        $value = strtolower(trim($value));
        if (strpos($value, 'nikah') !== false) return 'Menikah';
        if (strpos($value, 'lajang') !== false || strpos($value, 'belum') !== false) return 'Lajang';
        if (strpos($value, 'duda') !== false || strpos($value, 'janda') !== false) return 'Duda/Janda';
        return 'Lajang';
    }

    private function cleanStatusAnggota($value)
    {
        $value = strtolower(trim($value));
        if (strpos($value, 'dewasa') !== false) return 'Dewasa';
        if (strpos($value, 'madya') !== false) return 'Madya';
        if (strpos($value, 'utama') !== false) return 'Utama';
        return null;
    }

    private function nullifyTrash($value)
    {
        $value = trim($value);
        if ($value === '' || $value === '-' || strpos($value, '₩') !== false) {
            return null;
        }
        return $value;
    }
}
