<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateExistingDataToHistories extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // 1. Fetch job_fields to map name to ID
        $jobFields = $db->table('job_fields')->get()->getResultArray();
        
        $mapJobField = function($name) use ($jobFields) {
            if (empty($name)) return null;
            $nameLower = strtolower(trim($name));
            
            if (strpos($nameLower, 'pendidikan') !== false || strpos($nameLower, 'guru') !== false || strpos($nameLower, 'dosen') !== false || strpos($nameLower, 'pengajaran') !== false || strpos($nameLower, 'mengajar') !== false || strpos($nameLower, 'akademis') !== false) {
                return 3; // Pendidikan / Akademis
            }
            if (strpos($nameLower, 'it') !== false || strpos($nameLower, 'teknologi') !== false || strpos($nameLower, 'komputer') !== false || strpos($nameLower, 'programmer') !== false || strpos($nameLower, 'software') !== false || strpos($nameLower, 'sistem info') !== false) {
                return 1; // IT / Teknologi Informasi
            }
            if (strpos($nameLower, 'medis') !== false || strpos($nameLower, 'kesehatan') !== false || strpos($nameLower, 'dokter') !== false || strpos($nameLower, 'perawat') !== false || strpos($nameLower, 'bidan') !== false || strpos($nameLower, 'apoteker') !== false || strpos($nameLower, 'klinik') !== false || strpos($nameLower, 'rs') !== false) {
                return 2; // Kesehatan / Medis
            }
            if (strpos($nameLower, 'keuangan') !== false || strpos($nameLower, 'bank') !== false || strpos($nameLower, 'akuntan') !== false || strpos($nameLower, 'pajak') !== false || strpos($nameLower, 'finance') !== false) {
                return 4; // Keuangan / Perbankan
            }
            if (strpos($nameLower, 'dagang') !== false || strpos($nameLower, 'wirausaha') !== false || strpos($nameLower, 'retail') !== false || strpos($nameLower, 'toko') !== false || strpos($nameLower, 'online') !== false || strpos($nameLower, 'sales') !== false || strpos($nameLower, 'marketing') !== false || strpos($nameLower, 'kuliner') !== false || strpos($nameLower, 'makanan') !== false || strpos($nameLower, 'warung') !== false || strpos($nameLower, 'bisnis') !== false || strpos($nameLower, 'jasa') !== false || strpos($nameLower, 'usaha') !== false) {
                return 5; // Perdagangan / Retail / E-commerce
            }
            if (strpos($nameLower, 'tani') !== false || strpos($nameLower, 'peternak') !== false || strpos($nameLower, 'kebun') !== false || strpos($nameLower, 'pertanian') !== false || strpos($nameLower, 'perkebunan') !== false || strpos($nameLower, 'peternakan') !== false) {
                return 6; // Pertanian / Perkebunan / Peternakan
            }
            if (strpos($nameLower, 'konstruksi') !== false || strpos($nameLower, 'bangunan') !== false || strpos($nameLower, 'properti') !== false || strpos($nameLower, 'arsitek') !== false || strpos($nameLower, 'sipil') !== false || strpos($nameLower, 'real estate') !== false) {
                return 7; // Konstruksi / Real Estate
            }
            if (strpos($nameLower, 'transportasi') !== false || strpos($nameLower, 'logistik') !== false || strpos($nameLower, 'driver') !== false || strpos($nameLower, 'ojek') !== false || strpos($nameLower, 'kurir') !== false || strpos($nameLower, 'gojek') !== false || strpos($nameLower, 'grab') !== false || strpos($nameLower, 'pengiriman') !== false) {
                return 8; // Transportasi / Logistik
            }
            if (strpos($nameLower, 'seni') !== false || strpos($nameLower, 'desain') !== false || strpos($nameLower, 'kreatif') !== false || strpos($nameLower, 'media') !== false || strpos($nameLower, 'editor') !== false || strpos($nameLower, 'streaming') !== false || strpos($nameLower, 'video') !== false || strpos($nameLower, 'desainer') !== false || strpos($nameLower, 'hiburan') !== false) {
                return 9; // Seni / Kreatif / Media / Hiburan
            }
            if (strpos($nameLower, 'pns') !== false || strpos($nameLower, 'pemerintah') !== false || strpos($nameLower, 'tni') !== false || strpos($nameLower, 'polri') !== false || strpos($nameLower, 'militer') !== false || strpos($nameLower, 'asn') !== false || strpos($nameLower, 'publik') !== false || strpos($nameLower, 'kelurahan') !== false || strpos($nameLower, 'kecamatan') !== false) {
                return 10; // Pemerintahan / Pelayanan Publik / Militer
            }
            if (strpos($nameLower, 'hukum') !== false || strpos($nameLower, 'legal') !== false || strpos($nameLower, 'advokat') !== false || strpos($nameLower, 'notaris') !== false || strpos($nameLower, 'pengacara') !== false) {
                return 11; // Hukum / Legal
            }
            if (strpos($nameLower, 'pabrik') !== false || strpos($nameLower, 'manufaktur') !== false || strpos($nameLower, 'industri') !== false || strpos($nameLower, 'operator mesin') !== false || strpos($nameLower, 'produksi') !== false) {
                return 12; // Manufaktur / Pabrik
            }
            
            foreach ($jobFields as $jf) {
                if (strpos($nameLower, strtolower($jf['name'])) !== false) {
                    return $jf['id'];
                }
            }
            
            return 13; // Lainnya
        };

        // 2. Fetch all members with relevant data
        $members = $db->table('members')
            ->select('id, pekerjaan, nama_instansi, alamat_kerja, bidang_pekerjaan, keahlian, pendapatan_range, pendidikan_terakhir, sekolah_universitas')
            ->get()
            ->getResultArray();

        $eduBatch = [];
        $workBatch = [];
        $skillBatch = [];

        $now = date('Y-m-d H:i:s');

        foreach ($members as $m) {
            $hasEdu = $db->table('member_educations')->where('member_id', $m['id'])->countAllResults() > 0;
            $hasWork = $db->table('member_work_experiences')->where('member_id', $m['id'])->countAllResults() > 0;
            $hasSkill = $db->table('member_skills')->where('member_id', $m['id'])->countAllResults() > 0;

            // A. Education
            if (!$hasEdu && !empty($m['pendidikan_terakhir'])) {
                $jenjangRaw = trim($m['pendidikan_terakhir']);
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
                
                $eduBatch[] = [
                    'member_id' => $m['id'],
                    'jenjang' => $jenjang,
                    'nama_institusi' => !empty($m['sekolah_universitas']) ? $m['sekolah_universitas'] : 'Institusi Pendidikan',
                    'rentang_tahun' => '',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            // B. Work Experience
            if (!$hasWork && (!empty($m['pekerjaan']) || !empty($m['nama_instansi']) || !empty($m['bidang_pekerjaan']) || !empty($m['pendapatan_range']) || !empty($m['alamat_kerja']))) {
                $bidangId = $mapJobField($m['bidang_pekerjaan']);
                $workBatch[] = [
                    'member_id' => $m['id'],
                    'jabatan' => !empty($m['pekerjaan']) ? $m['pekerjaan'] : 'Karyawan / Pegawai',
                    'nama_perusahaan' => !empty($m['nama_instansi']) ? $m['nama_instansi'] : 'Tempat Kerja / Instansi',
                    'bidang_pekerjaan_id' => $bidangId,
                    'pendapatan_range' => $m['pendapatan_range'] ?? '',
                    'alamat_kerja' => $m['alamat_kerja'] ?? '',
                    'rentang_tahun' => 'Saat Ini',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

            // C. Skills
            if (!$hasSkill && !empty($m['keahlian'])) {
                $skillBatch[] = [
                    'member_id' => $m['id'],
                    'nama_keahlian' => $m['keahlian'],
                    'keterangan' => 'Keahlian Utama',
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }
        }

        if (!empty($eduBatch)) {
            $db->table('member_educations')->insertBatch($eduBatch);
        }
        if (!empty($workBatch)) {
            $db->table('member_work_experiences')->insertBatch($workBatch);
        }
        if (!empty($skillBatch)) {
            $db->table('member_skills')->insertBatch($skillBatch);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('member_educations')->truncate();
        $db->table('member_work_experiences')->truncate();
        $db->table('member_skills')->truncate();
    }
}
