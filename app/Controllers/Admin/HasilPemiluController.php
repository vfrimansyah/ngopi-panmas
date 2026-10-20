<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class HasilPemiluController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get filters from request
        $selectedYear = $this->request->getGet('tahun') ?: '2024';
        $selectedTingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        if (!in_array($selectedTingkat, ['DPRD_KOTA', 'DPRD_PROV', 'DPR_RI'])) {
            $selectedTingkat = 'DPRD_KOTA';
        }
        $selectedDistrict = $this->request->getGet('district_id') ?: '327601'; // Default: Pancoran Mas
        $selectedVillage = $this->request->getGet('village_id');
        
        // Validate village belongs to district
        if (!empty($selectedVillage)) {
            $chk = $db->table('reg_villages')
                ->where('id', $selectedVillage)
                ->where('district_id', $selectedDistrict)
                ->countAllResults();
            if ($chk === 0) {
                $selectedVillage = null;
            }
        }
        
        // Get available election years
        $yearsQuery = $db->table('pemilu_suara_tps')
            ->select('DISTINCT(tahun) as tahun')
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();
        $years = array_column($yearsQuery, 'tahun');
        if (empty($years)) {
            $years = ['2024'];
        }
        
        // Get districts in Depok (Regency 3276)
        $districts = $db->table('reg_districts')
            ->where('regency_id', '3276')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get Kelurahans under selected district
        $villages = $db->table('reg_villages')
            ->where('district_id', $selectedDistrict)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
            
        // Get PKS Party ID
        $pksParty = $db->table('pemilu_partai')
            ->where('nama_partai LIKE', '%Keadilan Sejahtera%')
            ->orWhere('nama_partai LIKE', '%PKS%')
            ->get()
            ->getRowArray();
        $pksId = $pksParty['id'] ?? 8;
            
        // 1. Get Summary of Votes
        $summaryQuery = $db->table('pemilu_suara_tps st')
            ->select('SUM(st.suara_sah) as suara_sah, SUM(st.suara_tidak_sah) as suara_tidak_sah, SUM(st.suara_total) as suara_total, COUNT(DISTINCT st.tps_id) as tps_count')
            ->join('pemilu_tps t', 'st.tps_id = t.id')
            ->where('st.tahun', $selectedYear)
            ->where('st.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict);
            
        if (!empty($selectedVillage)) {
            $summaryQuery->where('t.village_id', $selectedVillage);
        }
        
        $summary = $summaryQuery->get()->getRowArray();
        
        // 2. Get Party Votes
        $partyVotesSub = $db->table('pemilu_suara_partai sp')
            ->select('sp.partai_id, SUM(sp.jumlah_suara) as suara_partai')
            ->join('pemilu_tps t', 'sp.tps_id = t.id')
            ->where('sp.tahun', $selectedYear)
            ->where('sp.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict);
        if (!empty($selectedVillage)) {
            $partyVotesSub->where('t.village_id', $selectedVillage);
        }
        $partyVotesSub->groupBy('sp.partai_id');
        $partyVotesRaw = $partyVotesSub->get()->getResultArray();
        
        // 3. Get Candidate Votes sum
        $calegVotesSub = $db->table('pemilu_suara_caleg sc')
            ->select('c.partai_id, SUM(sc.jumlah_suara) as suara_caleg')
            ->join('pemilu_caleg c', 'sc.caleg_id = c.id')
            ->join('pemilu_tps t', 'sc.tps_id = t.id')
            ->where('sc.tahun', $selectedYear)
            ->where('c.tahun', $selectedYear)
            ->where('c.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict);
        if (!empty($selectedVillage)) {
            $calegVotesSub->where('t.village_id', $selectedVillage);
        }
        $calegVotesSub->groupBy('c.partai_id');
        $calegVotesRaw = $calegVotesSub->get()->getResultArray();
        
        // 4. Map them together
        $parties = $db->table('pemilu_partai p')
            ->select('p.id, p.nama_partai, p.lambang_partai, pe.no_urut')
            ->join('pemilu_peserta pe', 'pe.partai_id = p.id AND pe.tahun = ' . $selectedYear, 'inner')
            ->orderBy('pe.no_urut', 'ASC')
            ->get()
            ->getResultArray();
            
        $partyVotesMap = [];
        foreach ($partyVotesRaw as $r) {
            $partyVotesMap[$r['partai_id']] = intval($r['suara_partai']);
        }
        $calegVotesMap = [];
        foreach ($calegVotesRaw as $r) {
            $calegVotesMap[$r['partai_id']] = intval($r['suara_caleg']);
        }
        
        $partyResults = [];
        $totalValidVotesComputed = 0;
        foreach ($parties as $p) {
            $suaraPartai = $partyVotesMap[$p['id']] ?? 0;
            $suaraCaleg = $calegVotesMap[$p['id']] ?? 0;
            $totalSuara = $suaraPartai + $suaraCaleg;
            $totalValidVotesComputed += $totalSuara;
            
            $partyResults[] = [
                'id'             => $p['id'],
                'nama_partai'    => $p['nama_partai'],
                'lambang_partai' => $p['lambang_partai'],
                'no_urut'        => $p['no_urut'] ?? '-',
                'suara_partai'   => $suaraPartai,
                'suara_caleg'    => $suaraCaleg,
                'total_suara'    => $totalSuara
            ];
        }
        
        // Sort party results by total_suara DESC
        usort($partyResults, function($a, $b) {
            return $b['total_suara'] - $a['total_suara'];
        });
        
        // Find PKS rank and stats
        $pksRank = 0;
        $pksStats = null;
        foreach ($partyResults as $index => $r) {
            if ($r['id'] == $pksId) {
                $pksRank = $index + 1;
                $pksStats = $r;
                break;
            }
        }
        
        // 5. Get Top 5 RWs for PKS in each Kelurahan
        $topRwsPerKelurahan = [];
        foreach ($villages as $v) {
            $pksRwQuery = $db->query("
                SELECT 
                    t.rw, 
                    SUM(votes) as total_suara
                FROM (
                    -- Party votes for PKS
                    SELECT tps_id, jumlah_suara as votes 
                    FROM pemilu_suara_partai 
                    WHERE tahun = :tahun: AND partai_id = :pks_id: AND tingkat_pemilu = :tingkat_pemilu:
                    
                    UNION ALL
                    
                    -- Caleg votes for PKS candidates
                    SELECT sc.tps_id, sc.jumlah_suara as votes 
                    FROM pemilu_suara_caleg sc
                    JOIN pemilu_caleg c ON sc.caleg_id = c.id
                    WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.partai_id = :pks_id: AND c.tingkat_pemilu = :tingkat_pemilu:
                ) v
                JOIN pemilu_tps t ON v.tps_id = t.id
                WHERE t.village_id = :village_id:
                GROUP BY t.rw
                ORDER BY total_suara DESC
                LIMIT 5
            ", [
                'tahun'          => $selectedYear,
                'pks_id'         => $pksId,
                'tingkat_pemilu' => $selectedTingkat,
                'village_id'     => $v['id']
            ]);
            
            $topRwsPerKelurahan[$v['name']] = $pksRwQuery->getResultArray();
        }
        
        // 6. Get All Parties that have Candidates for the Heatmap filter
        $allPartiesWithCaleg = $db->query("
            SELECT p.id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as no_urut, COUNT(c.id) as caleg_count
            FROM pemilu_partai p
            JOIN pemilu_caleg c ON c.partai_id = p.id AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat_pemilu:
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            GROUP BY p.id, p.nama_partai, p.lambang_partai, pe.no_urut
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC
        ", ['tahun' => (int)$selectedYear, 'tingkat_pemilu' => $selectedTingkat])->getResultArray();

        // 7. Get Initial Candidates for PKS (default party)
        $initialCalegList = $db->table('pemilu_caleg')
            ->where('partai_id', $pksId)
            ->where('tahun', $selectedYear)
            ->where('tingkat_pemilu', $selectedTingkat)
            ->orderBy('no_urut', 'ASC')
            ->get()
            ->getResultArray();

        // 8. Top 20 Candidates with Village Breakdown & Percentage Heatmap
        $alokasiKursi = ($selectedTingkat === 'DPRD_PROV') ? 11 : 6;

        // Sequence of villages matching Pancoran Mas order: Depok, Depok Jaya, Mampang, Pancoran Mas, Rangkapan Jaya, Rangkapan Jaya Baru
        $villageOrderPriority = [
            '3276011006' => 1, // Depok
            '3276011007' => 2, // Depok Jaya
            '3276011009' => 3, // Mampang
            '3276011008' => 4, // Pancoran Mas
            '3276011011' => 5, // Rangkapan Jaya
            '3276011010' => 6, // Rangkapan Jaya Baru
        ];

        $villagesOrdered = $villages;
        usort($villagesOrdered, function($a, $b) use ($villageOrderPriority) {
            $pA = $villageOrderPriority[$a['id']] ?? 99;
            $pB = $villageOrderPriority[$b['id']] ?? 99;
            return $pA <=> $pB;
        });

        // Total caleg votes per village for percentage denominator
        $villageTotalsQuery = $db->table('pemilu_suara_caleg sc')
            ->select('t.village_id, SUM(sc.jumlah_suara) as total_votes')
            ->join('pemilu_caleg c', 'sc.caleg_id = c.id')
            ->join('pemilu_tps t', 'sc.tps_id = t.id')
            ->where('sc.tahun', $selectedYear)
            ->where('c.tahun', $selectedYear)
            ->where('c.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict)
            ->groupBy('t.village_id')
            ->get()
            ->getResultArray();

        $villageCalegTotals = [];
        $grandTotalCalegVotes = 0;
        foreach ($villageTotalsQuery as $vt) {
            $villageCalegTotals[$vt['village_id']] = (int)$vt['total_votes'];
            $grandTotalCalegVotes += (int)$vt['total_votes'];
        }

        // Top 20 candidates across all parties in district
        $top20CandidatesQuery = $db->query("
            SELECT 
                c.id,
                c.no_urut,
                c.nama_caleg,
                p.id as partai_id,
                p.nama_partai,
                p.lambang_partai,
                COALESCE(MAX(pe.no_urut), p.id) as partai_no_urut,
                SUM(sc.jumlah_suara) as total_suara
            FROM pemilu_caleg c
            JOIN pemilu_partai p ON c.partai_id = p.id
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            JOIN pemilu_suara_caleg sc ON sc.caleg_id = c.id
            JOIN pemilu_tps t ON sc.tps_id = t.id
            WHERE c.tahun = :tahun: 
              AND sc.tahun = :tahun: 
              AND c.tingkat_pemilu = :tingkat: 
              AND t.district_id = :district:
            GROUP BY c.id, c.no_urut, c.nama_caleg, p.id, p.nama_partai, p.lambang_partai
            ORDER BY total_suara DESC
            LIMIT 20
        ", [
            'tahun'    => (int)$selectedYear,
            'tingkat'  => $selectedTingkat,
            'district' => $selectedDistrict
        ])->getResultArray();

        $top20CalegIds = array_column($top20CandidatesQuery, 'id');
        $top20Breakdown = [];
        if (!empty($top20CalegIds)) {
            $top20BreakdownQuery = $db->table('pemilu_suara_caleg sc')
                ->select('sc.caleg_id, t.village_id, SUM(sc.jumlah_suara) as votes')
                ->join('pemilu_tps t', 'sc.tps_id = t.id')
                ->where('sc.tahun', $selectedYear)
                ->whereIn('sc.caleg_id', $top20CalegIds)
                ->groupBy('sc.caleg_id, t.village_id')
                ->get()
                ->getResultArray();
            foreach ($top20BreakdownQuery as $row) {
                $top20Breakdown[$row['caleg_id']][$row['village_id']] = (int)$row['votes'];
            }
        }

        $top20Caleg = [];
        foreach ($top20CandidatesQuery as $idx => $c) {
            $cId = $c['id'];
            $vList = [];
            foreach ($villagesOrdered as $v) {
                $vId = $v['id'];
                $vVotes = $top20Breakdown[$cId][$vId] ?? 0;
                $vDenom = $villageCalegTotals[$vId] ?? 0;
                $vPct = $vDenom > 0 ? ($vVotes / $vDenom) * 100 : 0;
                $vList[$vId] = [
                    'village_id'   => $vId,
                    'village_name' => $v['name'],
                    'votes'        => $vVotes,
                    'percent'      => $vPct,
                ];
            }
            $cTotalVotes = (int)$c['total_suara'];
            $cTotalPct = $grandTotalCalegVotes > 0 ? ($cTotalVotes / $grandTotalCalegVotes) * 100 : 0;

            $top20Caleg[] = [
                'rank'           => $idx + 1,
                'id'             => $c['id'],
                'no_urut'        => $c['no_urut'],
                'nama_caleg'     => $c['nama_caleg'],
                'partai_id'      => $c['partai_id'],
                'nama_partai'    => $c['nama_partai'],
                'lambang_partai' => $c['lambang_partai'],
                'partai_no_urut' => $c['partai_no_urut'],
                'is_pks'         => ($c['partai_id'] == $pksId),
                'is_kursi'       => ($idx < $alokasiKursi),
                'villages'       => $vList,
                'total_suara'    => $cTotalVotes,
                'total_percent'  => $cTotalPct,
            ];
        }

        // 9. Peta Kekuatan Partai per Kelurahan (Matrix Heatmap) - SUARA TOTAL (PARTAI + CALEG)
        // 9a. Suara Partai (pemilu_suara_partai)
        $partyOnlyVillageQuery = $db->table('pemilu_suara_partai sp')
            ->select('sp.partai_id, t.village_id, SUM(sp.jumlah_suara) as votes')
            ->join('pemilu_tps t', 'sp.tps_id = t.id')
            ->where('sp.tahun', $selectedYear)
            ->where('sp.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict)
            ->groupBy('sp.partai_id, t.village_id')
            ->get()
            ->getResultArray();

        $partyOnlyVillageMap = [];
        foreach ($partyOnlyVillageQuery as $pv) {
            $partyOnlyVillageMap[$pv['partai_id']][$pv['village_id']] = (int)$pv['votes'];
        }

        // 9b. Suara Caleg (pemilu_suara_caleg)
        $calegOnlyVillageQuery = $db->table('pemilu_suara_caleg sc')
            ->select('c.partai_id, t.village_id, SUM(sc.jumlah_suara) as votes')
            ->join('pemilu_caleg c', 'sc.caleg_id = c.id')
            ->join('pemilu_tps t', 'sc.tps_id = t.id')
            ->where('sc.tahun', $selectedYear)
            ->where('c.tahun', $selectedYear)
            ->where('c.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict)
            ->groupBy('c.partai_id, t.village_id')
            ->get()
            ->getResultArray();

        $calegOnlyVillageMap = [];
        foreach ($calegOnlyVillageQuery as $cv) {
            $calegOnlyVillageMap[$cv['partai_id']][$cv['village_id']] = (int)$cv['votes'];
        }

        // 9c. Hitung Total Suara Sah per Kelurahan (Denominator = Partai + Caleg)
        $villageTotalSah = [];
        $districtTotalSah = 0;
        foreach ($villagesOrdered as $v) {
            $vId = $v['id'];
            $vTot = 0;
            foreach ($partyOnlyVillageMap as $pId => $vMap) {
                $vTot += $vMap[$vId] ?? 0;
            }
            foreach ($calegOnlyVillageMap as $pId => $vMap) {
                $vTot += $vMap[$vId] ?? 0;
            }
            $villageTotalSah[$vId] = $vTot;
            $districtTotalSah += $vTot;
        }

        $partyListQuery = $db->query("
            SELECT p.id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as no_urut
            FROM pemilu_partai p
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC
        ", ['tahun' => (int)$selectedYear])->getResultArray();

        $partyShortMap = [
            'Partai Keadilan Sejahtera' => 'PKS',
            'Partai Gerakan Indonesia Raya' => 'GERINDRA',
            'Partai Gerindra' => 'GERINDRA',
            'Partai Persatuan Pembangunan' => 'PPP',
            'Partai Golongan Karya' => 'GOLKAR',
            'Partai Golkar' => 'GOLKAR',
            'Partai Demokrasi Indonesia Perjuangan' => 'PDIP',
            'Partai Perindo' => 'PERINDO',
            'Partai Persatuan Indonesia' => 'PERINDO',
            'Partai Demokrat' => 'DEMOKRAT',
            'Partai Amanat Nasional' => 'PAN',
            'Partai Kebangkitan Bangsa' => 'PKB',
            'Partai Solidaritas Indonesia' => 'PSI',
            'Partai NasDem' => 'NASDEM',
            'Partai Nasdem' => 'NASDEM',
            'Partai Gelombang Rakyat Indonesia' => 'GELORA',
            'Partai Gelora' => 'GELORA',
            'Partai Buruh' => 'BURUH',
            'Partai Ummat' => 'UMMAT',
            'Partai Kebangkitan Nusantara' => 'PKN',
            'Partai Bulan Bintang' => 'PBB',
            'Partai Garda Perubahan Indonesia' => 'GARUDA',
            'Partai Garda Republik Indonesia' => 'GARUDA',
            'Partai Hati Nurani Rakyat' => 'HANURA',
        ];

        $target10Partai = ['PKB', 'GERINDRA', 'PDIP', 'GOLKAR', 'NASDEM', 'PKS', 'PAN', 'DEMOKRAT', 'PSI', 'PPP'];

        $partyStrengthMatrix = [];
        foreach ($partyListQuery as $p) {
            $pId = $p['id'];
            $pName = $p['nama_partai'];
            $acronym = $pName;
            foreach ($partyShortMap as $k => $v) {
                if (stripos($pName, $k) !== false) {
                    $acronym = $v;
                    break;
                }
            }

            $vList = [];
            $totalPartyVotes = 0;
            $totalPartyPartaiOnly = 0;
            $totalPartyCalegOnly = 0;
            foreach ($villagesOrdered as $v) {
                $vId = $v['id'];
                $spVotes = $partyOnlyVillageMap[$pId][$vId] ?? 0;
                $scVotes = $calegOnlyVillageMap[$pId][$vId] ?? 0;
                $totVotes = $spVotes + $scVotes;

                $totalPartyVotes += $totVotes;
                $totalPartyPartaiOnly += $spVotes;
                $totalPartyCalegOnly += $scVotes;

                $vDenom = $villageTotalSah[$vId] ?? 0;
                $pct = $vDenom > 0 ? ($totVotes / $vDenom) * 100 : 0;
                $vList[$vId] = [
                    'village_id'   => $vId,
                    'village_name' => $v['name'],
                    'votes'        => $totVotes,
                    'suara_partai' => $spVotes,
                    'suara_caleg'  => $scVotes,
                    'percent'      => $pct,
                ];
            }

            $totalPct = $districtTotalSah > 0 ? ($totalPartyVotes / $districtTotalSah) * 100 : 0;

            $partyStrengthMatrix[] = [
                'id'             => $pId,
                'no_urut'        => $p['no_urut'],
                'nama_partai'    => $pName,
                'akronim'        => $acronym,
                'lambang_partai' => $p['lambang_partai'],
                'is_pks'         => ($pId == $pksId),
                'is_top10'       => in_array($acronym, $target10Partai),
                'has_votes'      => ($totalPartyVotes > 0),
                'villages'       => $vList,
                'total_suara'    => $totalPartyVotes,
                'total_partai'   => $totalPartyPartaiOnly,
                'total_caleg'    => $totalPartyCalegOnly,
                'total_percent'  => $totalPct,
            ];
        }

        $data = [
            'title'               => $selectedTingkat === 'DPRD_PROV' ? 'Dashboard Pemilu DPRD Provinsi Jawa Barat' : ($selectedTingkat === 'DPR_RI' ? 'Dashboard Pemilu DPR RI' : 'Dashboard Pemilu DPRD Kota Depok'),
            'years'               => $years,
            'selectedYear'        => $selectedYear,
            'selectedTingkat'     => $selectedTingkat,
            'tingkatOptions'      => [
                'DPRD_KOTA' => 'DPRD Kota Depok (Dapil 1 Pancoran Mas)',
                'DPRD_PROV' => 'DPRD Provinsi Jawa Barat (Dapil Jabar 8: Depok - Bekasi)',
                'DPR_RI'    => 'DPR RI (Dapil Jabar 6: Depok - Bekasi)',
            ],
            'districts'           => $districts,
            'selectedDistrict'    => $selectedDistrict,
            'villages'            => $villages,
            'selectedVillage'     => $selectedVillage,
            'summary'             => $summary,
            'partyResults'        => $partyResults,
            'totalValidVotes'     => $totalValidVotesComputed ?: ($summary['suara_sah'] ?? 0),
            'pksId'               => $pksId,
            'pksRank'             => $pksRank,
            'pksStats'            => $pksStats,
            'topRwsPerKelurahan'  => $topRwsPerKelurahan,
            'allPartiesWithCaleg' => $allPartiesWithCaleg,
            'initialCalegList'    => $initialCalegList,
            'partyStrengthMatrix' => $partyStrengthMatrix,
            'top20Caleg'          => $top20Caleg,
            'alokasiKursi'        => $alokasiKursi,
            'villagesOrdered'     => $villagesOrdered,
            'grandTotalCalegVotes'=> $grandTotalCalegVotes,
        ];
        
        $perms = $this->getPagePermissions('pemilu');
        $data = array_merge($data, [
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);

        return view('admin/pemilu_hasil/index', $data);
    }

    public function heatmap()
    {
        $db = \Config\Database::connect();

        // Get filters from request
        $selectedYear = $this->request->getGet('tahun') ?: '2024';
        $selectedTingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        if (!in_array($selectedTingkat, ['DPRD_KOTA', 'DPRD_PROV', 'DPR_RI'])) {
            $selectedTingkat = 'DPRD_KOTA';
        }
        $selectedRegency = $this->request->getGet('regency_id') ?: '3276'; // Default: Kota Depok
        $selectedDistrict = $this->request->getGet('district_id') ?: '327601'; // Default: Pancoran Mas
        $selectedVillage = $this->request->getGet('village_id');

        // Available election years
        $yearsQuery = $db->table('pemilu_suara_tps')
            ->select('DISTINCT(tahun) as tahun')
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();
        $years = array_column($yearsQuery, 'tahun');
        if (empty($years)) {
            $years = ['2024'];
        }

        // Regencies in Jawa Barat (Depok 3276 prioritized first)
        $regencies = $db->query("
            SELECT id, name 
            FROM reg_regencies 
            WHERE province_id = '32'
            ORDER BY CASE WHEN id = '3276' THEN 0 ELSE 1 END, name ASC
        ")->getResultArray();

        // Validate regency exists in list
        $regencyIds = array_column($regencies, 'id');
        if (!in_array($selectedRegency, $regencyIds)) {
            $selectedRegency = '3276';
        }

        // Districts under selected regency
        $districts = $db->table('reg_districts')
            ->where('regency_id', $selectedRegency)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Validate district belongs to regency
        $districtIds = array_column($districts, 'id');
        if (!in_array($selectedDistrict, $districtIds)) {
            $selectedDistrict = !empty($districts) ? $districts[0]['id'] : '327601';
        }

        // Kelurahans under selected district
        $villages = $db->table('reg_villages')
            ->where('district_id', $selectedDistrict)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Validate village belongs to district
        $villageIds = array_column($villages, 'id');
        if (empty($selectedVillage) || !in_array($selectedVillage, $villageIds)) {
            // Default to Kel. Depok (3276011006) if present, else first village
            $depokV = array_filter($villages, fn($v) => (strpos($v['name'], 'Depok') !== false && strpos($v['name'], 'Jaya') === false));
            $selectedVillage = !empty($depokV) ? reset($depokV)['id'] : ($villages[0]['id'] ?? null);
        }

        // PKS Party ID
        $pksParty = $db->table('pemilu_partai')
            ->where('nama_partai LIKE', '%Keadilan Sejahtera%')
            ->orWhere('nama_partai LIKE', '%PKS%')
            ->get()
            ->getRowArray();
        $pksId = $pksParty['id'] ?? 8;

        // All Parties that have Candidates for Filter
        $allPartiesWithCaleg = $db->query("
            SELECT p.id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as no_urut, COUNT(c.id) as caleg_count
            FROM pemilu_partai p
            JOIN pemilu_caleg c ON c.partai_id = p.id AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat_pemilu:
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            GROUP BY p.id, p.nama_partai, p.lambang_partai, pe.no_urut
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC
        ", ['tahun' => (int)$selectedYear, 'tingkat_pemilu' => $selectedTingkat])->getResultArray();

        // Initial candidates for PKS (for spatial map quick select if user uses spatial view)
        $initialCalegList = $db->table('pemilu_caleg')
            ->where('partai_id', $pksId)
            ->where('tahun', $selectedYear)
            ->where('tingkat_pemilu', $selectedTingkat)
            ->orderBy('no_urut', 'ASC')
            ->get()
            ->getResultArray();

        // All candidates across all parties for Tab 1 (Matrix Heatmap initial caleg dropdown)
        $allCalegList = $db->query("
            SELECT c.id, c.no_urut, c.nama_caleg, c.partai_id, p.nama_partai, COALESCE(pe.no_urut, p.id) as partai_no_urut
            FROM pemilu_caleg c
            JOIN pemilu_partai p ON c.partai_id = p.id
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            WHERE c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat_pemilu:
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC, c.no_urut ASC
        ", ['tahun' => (int)$selectedYear, 'tingkat_pemilu' => $selectedTingkat])->getResultArray();

        $data = [
            'title'               => 'Heatmap Hasil Pemilu',
            'years'               => $years,
            'selectedYear'        => $selectedYear,
            'selectedTingkat'     => $selectedTingkat,
            'tingkatOptions'      => [
                'DPRD_KOTA' => 'DPRD Kota Depok (Dapil 1 Pancoran Mas)',
                'DPRD_PROV' => 'DPRD Provinsi Jawa Barat (Dapil Jabar 8: Depok - Bekasi)',
                'DPR_RI'    => 'DPR RI (Dapil Jabar 6: Depok - Bekasi)',
            ],
            'regencies'           => $regencies,
            'selectedRegency'     => $selectedRegency,
            'districts'           => $districts,
            'selectedDistrict'    => $selectedDistrict,
            'villages'            => $villages,
            'selectedVillage'     => $selectedVillage,
            'allPartiesWithCaleg' => $allPartiesWithCaleg,
            'initialCalegList'    => $initialCalegList,
            'allCalegList'        => $allCalegList,
            'pksId'               => $pksId,
        ];

        $perms = $this->getPagePermissions('pemilu');
        $data = array_merge($data, [
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);

        return view('admin/pemilu_heatmap/index', $data);
    }

    public function getDistricts()
    {
        $regencyId = $this->request->getGet('regency_id') ?: '3276';
        $db = \Config\Database::connect();
        $districts = $db->table('reg_districts')
            ->where('regency_id', $regencyId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($districts);
    }

    public function getVillages()
    {
        $districtId = $this->request->getGet('district_id') ?: '327601';
        $db = \Config\Database::connect();
        $villages = $db->table('reg_villages')
            ->where('district_id', $districtId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($villages);
    }

    
    public function getCandidates()
    {
        $db = \Config\Database::connect();
        $partaiId = $this->request->getGet('partai_id');
        $selectedYear = $this->request->getGet('tahun') ?: '2024';
        $selectedTingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        $selectedDistrict = $this->request->getGet('district_id') ?: '327601';
        $selectedVillage = $this->request->getGet('village_id');
        
        if (empty($partaiId)) {
            return $this->response->setJSON([]);
        }
        
        $query = $db->table('pemilu_caleg c')
            ->select('c.no_urut, c.nama_caleg, SUM(sc.jumlah_suara) as total_suara')
            ->join('pemilu_suara_caleg sc', 'sc.caleg_id = c.id')
            ->join('pemilu_tps t', 'sc.tps_id = t.id')
            ->where('c.tahun', $selectedYear)
            ->where('sc.tahun', $selectedYear)
            ->where('c.partai_id', $partaiId)
            ->where('c.tingkat_pemilu', $selectedTingkat)
            ->where('t.district_id', $selectedDistrict);
            
        if (!empty($selectedVillage)) {
            $query->where('t.village_id', $selectedVillage);
        }
        
        $candidates = $query->groupBy('c.id')
            ->orderBy('total_suara', 'DESC')
            ->get()
            ->getResultArray();
            
        return $this->response->setJSON($candidates);
    }

    public function getCalegByParty()
    {
        $db = \Config\Database::connect();
        $partaiId = $this->request->getGet('partai_id');
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';

        if (empty($partaiId) || $partaiId === 'all') {
            $calegs = $db->query("
                SELECT c.id, c.no_urut, c.nama_caleg, c.partai_id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as partai_no_urut
                FROM pemilu_caleg c
                JOIN pemilu_partai p ON c.partai_id = p.id
                LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
                WHERE c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
                ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC, c.no_urut ASC
            ", ['tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();
            return $this->response->setJSON($calegs);
        }

        $calegs = $db->table('pemilu_caleg')
            ->select('id, no_urut, nama_caleg')
            ->where('partai_id', $partaiId)
            ->where('tahun', $tahun)
            ->where('tingkat_pemilu', $tingkat)
            ->orderBy('no_urut', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($calegs);
    }

    public function getHeatmapData()
    {
        $db = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        $districtId = $this->request->getGet('district_id') ?: '327601';
        $partaiId = $this->request->getGet('partai_id') ?: '8'; // Default PKS
        $calegId = $this->request->getGet('caleg_id') ?: 'all';

        $calegInfo = null;
        $partyInfo = null;

        if ($partaiId !== 'all') {
            $partyInfo = $db->table('pemilu_partai')
                ->where('id', $partaiId)
                ->get()
                ->getRowArray();
        }

        if ($calegId !== 'all' && is_numeric($calegId)) {
            // Specific candidate
            $calegInfo = $db->table('pemilu_caleg c')
                ->select('c.id, c.no_urut, c.nama_caleg, c.partai_id, p.nama_partai, p.lambang_partai, pe.no_urut as no_urut_partai')
                ->join('pemilu_partai p', 'c.partai_id = p.id')
                ->join('pemilu_peserta pe', 'pe.partai_id = p.id AND pe.tahun = ' . (int)$tahun, 'left')
                ->where('c.id', $calegId)
                ->get()
                ->getRowArray();

            $query = $db->query("
                SELECT 
                    v.id as village_id,
                    v.name as village_name,
                    COALESCE(SUM(sc.jumlah_suara), 0) as suara
                FROM reg_villages v
                LEFT JOIN pemilu_tps t ON t.village_id = v.id
                LEFT JOIN pemilu_suara_caleg sc ON sc.tps_id = t.id AND sc.caleg_id = :caleg_id: AND sc.tahun = :tahun:
                WHERE v.district_id = :district_id:
                GROUP BY v.id, v.name
                ORDER BY suara DESC
            ", [
                'caleg_id' => $calegId,
                'tahun' => $tahun,
                'district_id' => $districtId
            ]);
            $villageResults = $query->getResultArray();

            $rwQuery = $db->query("
                SELECT 
                    v.name as village_name,
                    v.id as village_id,
                    t.rw,
                    SUM(sc.jumlah_suara) as suara
                FROM pemilu_suara_caleg sc
                JOIN pemilu_tps t ON sc.tps_id = t.id
                JOIN reg_villages v ON t.village_id = v.id
                WHERE sc.caleg_id = :caleg_id: AND sc.tahun = :tahun: AND t.district_id = :district_id:
                GROUP BY v.name, v.id, t.rw
                ORDER BY suara DESC
                LIMIT 5
            ", [
                'caleg_id' => $calegId,
                'tahun' => $tahun,
                'district_id' => $districtId
            ]);
            $topRws = $rwQuery->getResultArray();

        } elseif ($partaiId !== 'all') {
            if ($calegId === 'party_only') {
                // Hanya Suara Coblos Logo Partai
                $query = $db->query("
                    SELECT 
                        v.id as village_id,
                        v.name as village_name,
                        COALESCE(SUM(sp.jumlah_suara), 0) as suara
                    FROM reg_villages v
                    LEFT JOIN pemilu_tps t ON t.village_id = v.id
                    LEFT JOIN pemilu_suara_partai sp ON sp.tps_id = t.id AND sp.partai_id = :partai_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
                    WHERE v.district_id = :district_id:
                    GROUP BY v.id, v.name
                    ORDER BY suara DESC
                ", [
                    'partai_id'   => $partaiId,
                    'tahun'       => $tahun,
                    'tingkat'     => $tingkat,
                    'district_id' => $districtId
                ]);
                $villageResults = $query->getResultArray();

                $topRws = $db->query("
                    SELECT 
                        v.name as village_name,
                        v.id as village_id,
                        t.rw,
                        SUM(sp.jumlah_suara) as suara
                    FROM pemilu_suara_partai sp
                    JOIN pemilu_tps t ON sp.tps_id = t.id
                    JOIN reg_villages v ON t.village_id = v.id
                    WHERE sp.partai_id = :partai_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat: AND t.district_id = :district_id:
                    GROUP BY v.name, v.id, t.rw
                    ORDER BY suara DESC
                    LIMIT 5
                ", [
                    'partai_id'   => $partaiId,
                    'tahun'       => $tahun,
                    'tingkat'     => $tingkat,
                    'district_id' => $districtId
                ])->getResultArray();

            } else {
                // Seluruh Caleg & Coblos Partai Ini (Total Suara Sah Partai = Suara Caleg + Suara Partai)
                $query = $db->query("
                    SELECT 
                        v.id as village_id,
                        v.name as village_name,
                        COALESCE(SUM(sub.suara), 0) as suara
                    FROM reg_villages v
                    LEFT JOIN pemilu_tps t ON t.village_id = v.id
                    LEFT JOIN (
                        SELECT sc_sub.tps_id, sc_sub.jumlah_suara as suara
                        FROM pemilu_suara_caleg sc_sub
                        JOIN pemilu_caleg c ON sc_sub.caleg_id = c.id
                        WHERE c.partai_id = :partai_id: AND sc_sub.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
                        
                        UNION ALL
                        
                        SELECT sp_sub.tps_id, sp_sub.jumlah_suara as suara
                        FROM pemilu_suara_partai sp_sub
                        WHERE sp_sub.partai_id = :partai_id: AND sp_sub.tahun = :tahun: AND sp_sub.tingkat_pemilu = :tingkat:
                    ) sub ON sub.tps_id = t.id
                    WHERE v.district_id = :district_id:
                    GROUP BY v.id, v.name
                    ORDER BY suara DESC
                ", [
                    'partai_id'   => $partaiId,
                    'tahun'       => $tahun,
                    'tingkat'     => $tingkat,
                    'district_id' => $districtId
                ]);
                $villageResults = $query->getResultArray();

                $topRws = $db->query("
                    SELECT 
                        v.name as village_name,
                        v.id as village_id,
                        sub.rw,
                        SUM(sub.suara) as suara
                    FROM (
                        SELECT t.village_id, t.rw, sc.jumlah_suara as suara
                        FROM pemilu_suara_caleg sc
                        JOIN pemilu_caleg c ON sc.caleg_id = c.id
                        JOIN pemilu_tps t ON sc.tps_id = t.id
                        WHERE c.partai_id = :partai_id: AND sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat: AND t.district_id = :district_id:
                        
                        UNION ALL
                        
                        SELECT t.village_id, t.rw, sp.jumlah_suara as suara
                        FROM pemilu_suara_partai sp
                        JOIN pemilu_tps t ON sp.tps_id = t.id
                        WHERE sp.partai_id = :partai_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat: AND t.district_id = :district_id:
                    ) sub
                    JOIN reg_villages v ON sub.village_id = v.id
                    GROUP BY v.name, v.id, sub.rw
                    ORDER BY suara DESC
                    LIMIT 5
                ", [
                    'partai_id'   => $partaiId,
                    'tahun'       => $tahun,
                    'tingkat'     => $tingkat,
                    'district_id' => $districtId
                ])->getResultArray();
            }

        } else {
            // All candidates of ALL parties + Coblos Partai (Total Suara Sah Se-Kecamatan)
            $query = $db->query("
                SELECT 
                    v.id as village_id,
                    v.name as village_name,
                    COALESCE(SUM(sub.suara), 0) as suara
                FROM reg_villages v
                LEFT JOIN pemilu_tps t ON t.village_id = v.id
                LEFT JOIN (
                    SELECT sc.tps_id, sc.jumlah_suara as suara
                    FROM pemilu_suara_caleg sc
                    JOIN pemilu_caleg c ON sc.caleg_id = c.id
                    WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
                    
                    UNION ALL
                    
                    SELECT sp.tps_id, sp.jumlah_suara as suara
                    FROM pemilu_suara_partai sp
                    WHERE sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
                ) sub ON sub.tps_id = t.id
                WHERE v.district_id = :district_id:
                GROUP BY v.id, v.name
                ORDER BY suara DESC
            ", [
                'tahun'       => $tahun,
                'tingkat'     => $tingkat,
                'district_id' => $districtId
            ]);
            $villageResults = $query->getResultArray();

            $topRws = $db->query("
                SELECT 
                    v.name as village_name,
                    v.id as village_id,
                    sub.rw,
                    SUM(sub.suara) as suara
                FROM (
                    SELECT t.village_id, t.rw, sc.jumlah_suara as suara
                    FROM pemilu_suara_caleg sc
                    JOIN pemilu_caleg c ON sc.caleg_id = c.id
                    JOIN pemilu_tps t ON sc.tps_id = t.id
                    WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat: AND t.district_id = :district_id:
                    
                    UNION ALL
                    
                    SELECT t.village_id, t.rw, sp.jumlah_suara as suara
                    FROM pemilu_suara_partai sp
                    JOIN pemilu_tps t ON sp.tps_id = t.id
                    WHERE sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat: AND t.district_id = :district_id:
                ) sub
                JOIN reg_villages v ON sub.village_id = v.id
                GROUP BY v.name, v.id, sub.rw
                ORDER BY suara DESC
                LIMIT 5
            ", [
                'tahun'       => $tahun,
                'tingkat'     => $tingkat,
                'district_id' => $districtId
            ])->getResultArray();
        }

        $totalSuara = 0;
        $maxSuara = 0;
        $minSuara = PHP_INT_MAX;

        foreach ($villageResults as $r) {
            $val = (int)$r['suara'];
            $totalSuara += $val;
            if ($val > $maxSuara) $maxSuara = $val;
            if ($val < $minSuara) $minSuara = $val;
        }
        if ($minSuara === PHP_INT_MAX) $minSuara = 0;

        $rank = 1;
        $villageMap = [];
        foreach ($villageResults as &$vr) {
            $val = (int)$vr['suara'];
            $vr['percentage'] = $totalSuara > 0 ? round(($val / $totalSuara) * 100, 2) : 0;
            $vr['rank'] = $rank++;
            $villageMap[$vr['village_id']] = $vr;
        }

        if ($calegInfo) {
            $calegInfo['total_suara'] = $totalSuara;
        }

        return $this->response->setJSON([
            'status'       => 'success',
            'caleg_info'   => $calegInfo,
            'party_info'   => $partyInfo,
            'village_data' => $villageResults,
            'village_map'  => $villageMap,
            'top_rws'      => $topRws,
            'total_suara'  => $totalSuara,
            'max_suara'    => $maxSuara,
            'min_suara'    => $minSuara
        ]);
    }

    public function getRwBreakdown()
    {
        $db = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        $villageId = $this->request->getGet('village_id');
        $partaiId = $this->request->getGet('partai_id');
        $calegId = $this->request->getGet('caleg_id');

        if (empty($villageId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'village_id required']);
        }

        $village = $db->table('reg_villages')->where('id', $villageId)->get()->getRowArray();

        if ($calegId !== 'all' && is_numeric($calegId)) {
            $caleg = $db->table('pemilu_caleg c')
                ->select('c.nama_caleg, c.no_urut, p.nama_partai, p.lambang_partai')
                ->join('pemilu_partai p', 'c.partai_id = p.id')
                ->where('c.id', $calegId)
                ->get()
                ->getRowArray();

            $rows = $db->query("
                SELECT 
                    t.rw,
                    COALESCE(SUM(sc.jumlah_suara), 0) as suara,
                    COUNT(DISTINCT t.id) as tps_count
                FROM pemilu_tps t
                LEFT JOIN pemilu_suara_caleg sc ON sc.tps_id = t.id AND sc.caleg_id = :caleg_id: AND sc.tahun = :tahun:
                WHERE t.village_id = :village_id:
                GROUP BY t.rw
                ORDER BY CAST(t.rw AS UNSIGNED) ASC
            ", [
                'caleg_id' => $calegId,
                'tahun' => $tahun,
                'village_id' => $villageId
            ])->getResultArray();
        } elseif ($partaiId !== 'all' && is_numeric($partaiId)) {
            $party = $db->table('pemilu_partai')->where('id', $partaiId)->get()->getRowArray();
            if ($calegId === 'party_only') {
                $caleg = [
                    'nama_caleg'     => 'Hanya Suara Coblos ' . ($party['nama_partai'] ?? ''),
                    'nama_partai'    => $party['nama_partai'] ?? '',
                    'lambang_partai' => $party['lambang_partai'] ?? ''
                ];
                $rows = $db->query("
                    SELECT 
                        t.rw,
                        COALESCE(SUM(sp.jumlah_suara), 0) as suara,
                        COUNT(DISTINCT t.id) as tps_count
                    FROM pemilu_tps t
                    LEFT JOIN pemilu_suara_partai sp ON sp.tps_id = t.id AND sp.partai_id = :partai_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
                    WHERE t.village_id = :village_id:
                    GROUP BY t.rw
                    ORDER BY CAST(t.rw AS UNSIGNED) ASC
                ", [
                    'partai_id'  => $partaiId,
                    'tahun'      => $tahun,
                    'tingkat'    => $tingkat,
                    'village_id' => $villageId
                ])->getResultArray();
            } else {
                $caleg = [
                    'nama_caleg'     => 'Seluruh Caleg & Partai ' . ($party['nama_partai'] ?? ''),
                    'nama_partai'    => $party['nama_partai'] ?? '',
                    'lambang_partai' => $party['lambang_partai'] ?? ''
                ];

                $rows = $db->query("
                    SELECT 
                        t.rw,
                        COALESCE(SUM(sub.suara), 0) as suara,
                        COUNT(DISTINCT t.id) as tps_count
                    FROM pemilu_tps t
                    LEFT JOIN (
                        SELECT sc_sub.tps_id, sc_sub.jumlah_suara as suara
                        FROM pemilu_suara_caleg sc_sub
                        JOIN pemilu_caleg c ON sc_sub.caleg_id = c.id
                        WHERE c.partai_id = :partai_id: AND sc_sub.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
                        
                        UNION ALL
                        
                        SELECT sp_sub.tps_id, sp_sub.jumlah_suara as suara
                        FROM pemilu_suara_partai sp_sub
                        WHERE sp_sub.partai_id = :partai_id: AND sp_sub.tahun = :tahun: AND sp_sub.tingkat_pemilu = :tingkat:
                    ) sub ON sub.tps_id = t.id
                    WHERE t.village_id = :village_id:
                    GROUP BY t.rw
                    ORDER BY CAST(t.rw AS UNSIGNED) ASC
                ", [
                    'partai_id'  => $partaiId,
                    'tahun'      => $tahun,
                    'tingkat'    => $tingkat,
                    'village_id' => $villageId
                ])->getResultArray();
            }
        } else {
            $caleg = [
                'nama_caleg'     => 'Seluruh Caleg & Partai (Semua)',
                'nama_partai'    => 'Semua Partai',
                'lambang_partai' => ''
            ];
            $rows = $db->query("
                SELECT 
                    t.rw,
                    COALESCE(SUM(sub.suara), 0) as suara,
                    COUNT(DISTINCT t.id) as tps_count
                FROM pemilu_tps t
                LEFT JOIN (
                    SELECT sc.tps_id, sc.jumlah_suara as suara
                    FROM pemilu_suara_caleg sc
                    JOIN pemilu_caleg c ON sc.caleg_id = c.id
                    WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
                    
                    UNION ALL
                    
                    SELECT sp.tps_id, sp.jumlah_suara as suara
                    FROM pemilu_suara_partai sp
                    WHERE sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
                ) sub ON sub.tps_id = t.id
                WHERE t.village_id = :village_id:
                GROUP BY t.rw
                ORDER BY CAST(t.rw AS UNSIGNED) ASC
            ", [
                'tahun'      => $tahun,
                'tingkat'    => $tingkat,
                'village_id' => $villageId
            ])->getResultArray();
        }

        $totalSuara = 0;
        $maxSuara = 0;
        foreach ($rows as $r) {
            $val = (int)$r['suara'];
            $totalSuara += $val;
            if ($val > $maxSuara) $maxSuara = $val;
        }

        $sortedRows = $rows;
        usort($sortedRows, function($a, $b) {
            return (int)$b['suara'] - (int)$a['suara'];
        });
        $top3Rws = array_slice($sortedRows, 0, 3);

        return $this->response->setJSON([
            'status'       => 'success',
            'village'      => $village,
            'caleg'        => $caleg,
            'rw_list'      => $rows,
            'top_rws'      => $top3Rws,
            'total_suara'  => $totalSuara,
            'max_suara'    => $maxSuara
        ]);
    }

    public function getTpsHeatmapMatrix()
    {
        $db = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        $villageId = $this->request->getGet('village_id');
        $partaiId = $this->request->getGet('partai_id') ?: 'all';
        $rw = $this->request->getGet('rw') ?: 'all';

        // Default village if empty (Depok Jaya: 3276011007 or first village)
        if (empty($villageId)) {
            $firstV = $db->table('reg_villages')->where('district_id', '327601')->orderBy('name', 'ASC')->get()->getRowArray();
            $villageId = $firstV['id'] ?? '3276011007';
        }

        $village = $db->table('reg_villages')->where('id', $villageId)->get()->getRowArray();

        // 1. Get List of RWs in this village for filter
        $rwList = $db->query("
            SELECT DISTINCT rw
            FROM pemilu_tps
            WHERE village_id = :village_id: AND tahun = :tahun: AND rw IS NOT NULL AND rw != ''
            ORDER BY CAST(rw AS UNSIGNED) ASC
        ", ['village_id' => $villageId, 'tahun' => $tahun])->getResultArray();

        // 2. Get List of TPS in this village (filtered by RW if specified)
        $tpsQuery = "
            SELECT id, tps, rw, rt
            FROM pemilu_tps
            WHERE village_id = :village_id: AND tahun = :tahun:
        ";
        if (!empty($rw) && $rw !== 'all') {
            $tpsQuery .= " AND rw = " . $db->escape($rw);
        }
        $tpsQuery .= " ORDER BY CAST(REGEXP_REPLACE(tps, '[^0-9]', '') AS UNSIGNED) ASC";
        $tpsList = $db->query($tpsQuery, ['village_id' => $villageId, 'tahun' => $tahun])->getResultArray();

        // 3. Get Parties that have candidates
        $pQuery = "
            SELECT p.id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as no_urut
            FROM pemilu_partai p
            JOIN pemilu_caleg c ON c.partai_id = p.id AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
        ";
        if (!empty($partaiId) && $partaiId !== 'all') {
            $pQuery .= " WHERE p.id = " . (int)$partaiId;
        }
        $pQuery .= "
            GROUP BY p.id, p.nama_partai, p.lambang_partai, pe.no_urut
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC
        ";
        $parties = $db->query($pQuery, ['tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // 4. Get Candidates
        $cQuery = "
            SELECT id, partai_id, no_urut, nama_caleg
            FROM pemilu_caleg
            WHERE tahun = :tahun: AND tingkat_pemilu = :tingkat:
        ";
        if (!empty($partaiId) && $partaiId !== 'all') {
            $cQuery .= " AND partai_id = " . (int)$partaiId;
        }
        $cQuery .= " ORDER BY partai_id ASC, no_urut ASC";
        $candidates = $db->query($cQuery, ['tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // 5. Get Candidate Votes
        $vQuery = "
            SELECT sc.tps_id, sc.caleg_id, sc.jumlah_suara
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            JOIN pemilu_tps t ON sc.tps_id = t.id
            WHERE t.village_id = :village_id: AND sc.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
        ";
        if (!empty($partaiId) && $partaiId !== 'all') {
            $vQuery .= " AND sc.caleg_id IN (SELECT id FROM pemilu_caleg WHERE partai_id = " . (int)$partaiId . " AND tingkat_pemilu = " . $db->escape($tingkat) . ")";
        }
        if (!empty($rw) && $rw !== 'all') {
            $vQuery .= " AND t.rw = " . $db->escape($rw);
        }
        $votesRaw = $db->query($vQuery, ['village_id' => $villageId, 'tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // 6. Get Party Votes (Coblos Partai)
        $spQuery = "
            SELECT sp.tps_id, sp.partai_id, sp.jumlah_suara
            FROM pemilu_suara_partai sp
            JOIN pemilu_tps t ON sp.tps_id = t.id
            WHERE t.village_id = :village_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
        ";
        if (!empty($partaiId) && $partaiId !== 'all') {
            $spQuery .= " AND sp.partai_id = " . (int)$partaiId;
        }
        if (!empty($rw) && $rw !== 'all') {
            $spQuery .= " AND t.rw = " . $db->escape($rw);
        }
        $partyVotesRaw = $db->query($spQuery, ['village_id' => $villageId, 'tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // Index candidate votes
        $calegVotesMap = [];
        $maxVote = 0;
        $tpsColTotals = [];
        foreach ($tpsList as $t) {
            $tpsColTotals[$t['id']] = 0;
        }

        foreach ($votesRaw as $vr) {
            $cId = (int)$vr['caleg_id'];
            $tId = (int)$vr['tps_id'];
            $val = (int)$vr['jumlah_suara'];
            $calegVotesMap[$cId][$tId] = $val;
            if ($val > $maxVote) $maxVote = $val;
            if (isset($tpsColTotals[$tId])) {
                $tpsColTotals[$tId] += $val;
            }
        }

        // Index party votes
        $partyVotesMap = [];
        foreach ($partyVotesRaw as $pr) {
            $pId = (int)$pr['partai_id'];
            $tId = (int)$pr['tps_id'];
            $val = (int)$pr['jumlah_suara'];
            $partyVotesMap[$pId][$tId] = $val;
            if ($val > $maxVote) $maxVote = $val;
            if (isset($tpsColTotals[$tId])) {
                $tpsColTotals[$tId] += $val;
            }
        }

        // Group caleg by party
        $calegByParty = [];
        foreach ($candidates as $c) {
            $cId = (int)$c['id'];
            $cVotes = $calegVotesMap[$cId] ?? [];
            $totalC = array_sum($cVotes);
            $c['votes'] = $cVotes;
            $c['total_suara'] = $totalC;
            $calegByParty[$c['partai_id']][] = $c;
        }

        // Assemble structured parties with their candidates
        $structuredParties = [];
        $totalAllVotes = 0;
        foreach ($parties as $p) {
            $pId = (int)$p['id'];
            $pVotes = $partyVotesMap[$pId] ?? [];
            $totalPartai = array_sum($pVotes);
            $p['suara_partai_tps'] = $pVotes;
            $p['total_suara_partai'] = $totalPartai;
            $p['calegs'] = $calegByParty[$pId] ?? [];

            $totalCalegs = array_sum(array_column($p['calegs'], 'total_suara'));
            $p['total_suara_gabungan'] = $totalPartai + $totalCalegs;
            $totalAllVotes += $p['total_suara_gabungan'];

            $structuredParties[] = $p;
        }

        return $this->response->setJSON([
            'status'             => 'success',
            'village'            => $village,
            'rw_list'            => array_column($rwList, 'rw'),
            'tps_list'           => $tpsList,
            'parties'            => $structuredParties,
            'max_vote'           => $maxVote,
            'total_all_votes'    => $totalAllVotes,
            'tps_col_totals'     => $tpsColTotals
        ]);
    }

    public function exportTpsHeatmap()
    {
        $db = \Config\Database::connect();
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';
        $villageId = $this->request->getGet('village_id') ?: '3276011007';
        $partaiId = $this->request->getGet('partai_id') ?: 'all';
        $rw = $this->request->getGet('rw') ?: 'all';

        $village = $db->table('reg_villages')->where('id', $villageId)->get()->getRowArray();
        $villageName = $village['name'] ?? 'Pancoran_Mas';

        // 1. TPS
        $tpsQuery = "
            SELECT id, tps, rw, rt
            FROM pemilu_tps
            WHERE village_id = :village_id: AND tahun = :tahun:
        ";
        if (!empty($rw) && $rw !== 'all') {
            $tpsQuery .= " AND rw = " . $db->escape($rw);
        }
        $tpsQuery .= " ORDER BY CAST(REGEXP_REPLACE(tps, '[^0-9]', '') AS UNSIGNED) ASC";
        $tpsList = $db->query($tpsQuery, ['village_id' => $villageId, 'tahun' => $tahun])->getResultArray();

        // 2. Parties
        $pQuery = "
            SELECT p.id, p.nama_partai, COALESCE(pe.no_urut, p.id) as no_urut
            FROM pemilu_partai p
            JOIN pemilu_caleg c ON c.partai_id = p.id AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
        ";
        if (!empty($partaiId) && $partaiId !== 'all') {
            $pQuery .= " WHERE p.id = " . (int)$partaiId;
        }
        $pQuery .= "
            GROUP BY p.id, p.nama_partai, pe.no_urut
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC
        ";
        $parties = $db->query($pQuery, ['tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // 3. Candidates
        $cQuery = "
            SELECT id, partai_id, no_urut, nama_caleg
            FROM pemilu_caleg
            WHERE tahun = :tahun: AND tingkat_pemilu = :tingkat:
        ";
        if (!empty($partaiId) && $partaiId !== 'all') {
            $cQuery .= " AND partai_id = " . (int)$partaiId;
        }
        $cQuery .= " ORDER BY partai_id ASC, no_urut ASC";
        $candidates = $db->query($cQuery, ['tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // 4. Candidate Votes
        $vQuery = "
            SELECT sc.tps_id, sc.caleg_id, sc.jumlah_suara
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            JOIN pemilu_tps t ON sc.tps_id = t.id
            WHERE t.village_id = :village_id: AND sc.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
        ";
        $votesRaw = $db->query($vQuery, ['village_id' => $villageId, 'tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();
        $calegVotesMap = [];
        foreach ($votesRaw as $vr) {
            $calegVotesMap[(int)$vr['caleg_id']][(int)$vr['tps_id']] = (int)$vr['jumlah_suara'];
        }

        // 5. Party Votes
        $spQuery = "
            SELECT sp.tps_id, sp.partai_id, sp.jumlah_suara
            FROM pemilu_suara_partai sp
            JOIN pemilu_tps t ON sp.tps_id = t.id
            WHERE t.village_id = :village_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
        ";
        $partyVotesRaw = $db->query($spQuery, ['village_id' => $villageId, 'tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();
        $partyVotesMap = [];
        foreach ($partyVotesRaw as $pr) {
            $partyVotesMap[(int)$pr['partai_id']][(int)$pr['tps_id']] = (int)$pr['jumlah_suara'];
        }

        $filename = "Heatmap_Suara_TPS_{$villageName}_{$tingkat}_{$tahun}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header
        $header = ['Partai / Nama Caleg'];
        foreach ($tpsList as $t) {
            $header[] = $t['tps'] . ($t['rw'] ? ' (RW ' . $t['rw'] . ')' : '');
        }
        $header[] = 'Total Suara';
        fputcsv($output, $header);

        foreach ($parties as $p) {
            $pId = (int)$p['id'];
            // Party Header
            $pRow = [$p['no_urut'] . '. ' . $p['nama_partai']];
            foreach ($tpsList as $t) {
                $pRow[] = '';
            }
            $pRow[] = '';
            fputcsv($output, $pRow);

            // Coblos Partai
            $pVotes = $partyVotesMap[$pId] ?? [];
            $cpRow = ['Coblos Partai ' . $p['nama_partai']];
            $cpTotal = 0;
            foreach ($tpsList as $t) {
                $v = $pVotes[$t['id']] ?? 0;
                $cpRow[] = $v;
                $cpTotal += $v;
            }
            $cpRow[] = $cpTotal;
            fputcsv($output, $cpRow);

            // Caleg
            foreach ($candidates as $c) {
                if ((int)$c['partai_id'] === $pId) {
                    $cId = (int)$c['id'];
                    $cVotes = $calegVotesMap[$cId] ?? [];
                    $cRow = [$c['no_urut'] . '. ' . $c['nama_caleg']];
                    $cTotal = 0;
                    foreach ($tpsList as $t) {
                        $v = $cVotes[$t['id']] ?? 0;
                        $cRow[] = $v;
                        $cTotal += $v;
                    }
                    $cRow[] = $cTotal;
                    fputcsv($output, $cRow);
                }
            }
        }

        fclose($output);
        exit;
    }

    public function getTpsCalegBreakdown()
    {
        $db = \Config\Database::connect();
        $tpsId = $this->request->getGet('tps_id');
        $calegId = $this->request->getGet('caleg_id');
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';

        if (empty($tpsId)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'tps_id parameter is required'
            ]);
        }

        // 1. TPS Info
        $tps = $db->table('pemilu_tps t')
            ->select('t.id, t.tps, t.rw, t.rt, t.dpt, t.village_id, v.name as village_name, d.name as district_name, r.name as regency_name')
            ->join('reg_villages v', 't.village_id = v.id')
            ->join('reg_districts d', 't.district_id = d.id')
            ->join('reg_regencies r', 't.regency_id = r.id')
            ->where('t.id', $tpsId)
            ->get()
            ->getRowArray();

        if (!$tps) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Data TPS tidak ditemukan'
            ]);
        }

        // 2. All Caleg Votes at this TPS
        $allCalegVotes = $db->query("
            SELECT c.id, c.nama_caleg, c.no_urut, c.partai_id, p.nama_partai, p.lambang_partai,
                   COALESCE(pe.no_urut, p.id) as partai_no_urut,
                   COALESCE(sc.jumlah_suara, 0) as jumlah_suara
            FROM pemilu_caleg c
            JOIN pemilu_partai p ON c.partai_id = p.id
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            LEFT JOIN pemilu_suara_caleg sc ON sc.caleg_id = c.id AND sc.tps_id = :tps_id: AND sc.tahun = :tahun:
            WHERE c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat:
            ORDER BY COALESCE(sc.jumlah_suara, 0) DESC, c.no_urut ASC
        ", ['tps_id' => (int)$tpsId, 'tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        // 3. Party Votes (Coblos Logo Partai) at this TPS
        $allPartyVotes = $db->query("
            SELECT p.id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as partai_no_urut,
                   COALESCE(sp.jumlah_suara, 0) as jumlah_suara
            FROM pemilu_partai p
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            LEFT JOIN pemilu_suara_partai sp ON sp.partai_id = p.id AND sp.tps_id = :tps_id: AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat:
            ORDER BY CAST(COALESCE(pe.no_urut, p.id) AS UNSIGNED) ASC
        ", ['tps_id' => (int)$tpsId, 'tahun' => (int)$tahun, 'tingkat' => $tingkat])->getResultArray();

        $totalSuaraCaleg = array_sum(array_column($allCalegVotes, 'jumlah_suara'));
        $totalSuaraPartai = array_sum(array_column($allPartyVotes, 'jumlah_suara'));
        $totalSuaraSah = $totalSuaraCaleg + $totalSuaraPartai;
        $dpt = (int)($tps['dpt'] ?? 0);
        $partisipasi = $dpt > 0 ? round(($totalSuaraSah / $dpt) * 100, 1) : 0;

        $leaderboard = [];
        $calegRankOverall = 0;
        $selectedCalegInfo = null;

        foreach ($allCalegVotes as $idx => $c) {
            $rank = $idx + 1;
            $suara = (int)$c['jumlah_suara'];
            $pct = $totalSuaraSah > 0 ? round(($suara / $totalSuaraSah) * 100, 1) : 0;
            $c['rank'] = $rank;
            $c['persentase'] = $pct;
            $leaderboard[] = $c;

            if (!empty($calegId) && (int)$c['id'] === (int)$calegId) {
                $calegRankOverall = $rank;
                $selectedCalegInfo = $c;
            }
        }

        if ($selectedCalegInfo) {
            $inPartyList = array_values(array_filter($allCalegVotes, function($c) use ($selectedCalegInfo) {
                return (int)$c['partai_id'] === (int)$selectedCalegInfo['partai_id'];
            }));
            $calegRankInParty = 0;
            foreach ($inPartyList as $idx => $c) {
                if ((int)$c['id'] === (int)$calegId) {
                    $calegRankInParty = $idx + 1;
                    break;
                }
            }
            $selectedCalegInfo['rank_overall'] = $calegRankOverall;
            $selectedCalegInfo['total_calegs'] = count($allCalegVotes);
            $selectedCalegInfo['rank_in_party'] = $calegRankInParty;
            $selectedCalegInfo['total_in_party'] = count($inPartyList);
        }

        $topCaleg = !empty($leaderboard) ? $leaderboard[0] : null;

        // Group caleg sums by party
        $partyCalegSum = [];
        foreach ($allCalegVotes as $c) {
            $pId = (int)$c['partai_id'];
            $partyCalegSum[$pId] = ($partyCalegSum[$pId] ?? 0) + (int)$c['jumlah_suara'];
        }

        $partyStandings = [];
        foreach ($allPartyVotes as $p) {
            $pId = (int)$p['id'];
            $suaraPartai = (int)$p['jumlah_suara'];
            $suaraCaleg = $partyCalegSum[$pId] ?? 0;
            $totalPartai = $suaraPartai + $suaraCaleg;
            $pct = $totalSuaraSah > 0 ? round(($totalPartai / $totalSuaraSah) * 100, 1) : 0;

            $partyStandings[] = [
                'partai_id'      => $pId,
                'nama_partai'    => $p['nama_partai'],
                'lambang_partai' => $p['lambang_partai'],
                'partai_no_urut' => $p['partai_no_urut'],
                'suara_partai'   => $suaraPartai,
                'suara_caleg'    => $suaraCaleg,
                'total_suara'    => $totalPartai,
                'persentase'     => $pct
            ];
        }

        usort($partyStandings, function($a, $b) {
            return $b['total_suara'] - $a['total_suara'];
        });

        foreach ($partyStandings as $idx => &$ps) {
            $ps['rank'] = $idx + 1;
        }
        unset($ps);
        $topParty = !empty($partyStandings) ? $partyStandings[0] : null;

        // 4. Data Anggota di RW ini
        $villageName = $tps['village_name'] ?? '';
        $rwClean = preg_replace('/[^0-9]/', '', $tps['rw'] ?? '');
        $rwNum = !empty($rwClean) ? (int)$rwClean : 0;

        $members = [];
        $genderL = 0;
        $genderP = 0;
        if (!empty($villageName) && $rwNum > 0) {
            $members = $db->query("
                SELECT m.id, m.nama_lengkap, m.rt, m.rw, m.jenis_kelamin, m.status_anggota,
                       m.pekerjaan, m.pendidikan_terakhir, m.handphone, m.kode_upa
                FROM members m
                JOIN cms_region04_villages cv ON m.kelurahan_id = cv.id
                WHERE LOWER(TRIM(cv.name)) = LOWER(TRIM(:village_name:))
                  AND CAST(REGEXP_REPLACE(m.rw, '[^0-9]', '') AS UNSIGNED) = :rw_num:
                ORDER BY CAST(REGEXP_REPLACE(m.rt, '[^0-9]', '') AS UNSIGNED) ASC, m.nama_lengkap ASC
            ", [
                'village_name' => $villageName,
                'rw_num'       => $rwNum
            ])->getResultArray();

            foreach ($members as $m) {
                $jk = strtoupper(trim($m['jenis_kelamin'] ?? ''));
                if ($jk === 'L') $genderL++;
                elseif ($jk === 'P') $genderP++;
            }
        }

        // 5. Data Kegiatan di RW ini (dari pemilu_sisir_rw & kegiatan_list)
        $kegiatanList = [];
        if (!empty($villageName) && $rwNum > 0) {
            $sisirKegiatan = $db->query("
                SELECT s.id, s.nama_kegiatan, s.tanggal, s.alamat, s.rt, s.rw, s.jumlah_peserta,
                       s.tokoh_hadir, s.agenda_lanjutan, s.keterangan, s.status,
                       b.nama as bentuk_kegiatan, 'sisir_rw' as sumber
                FROM pemilu_sisir_rw s
                LEFT JOIN pemilu_bentuk_kegiatan b ON s.bentuk_kegiatan_id = b.id
                WHERE (s.village_id = :village_id: OR LOWER(TRIM(s.kelurahan)) = LOWER(TRIM(:village_name:)))
                  AND CAST(REGEXP_REPLACE(s.rw, '[^0-9]', '') AS UNSIGNED) = :rw_num:
                ORDER BY s.tanggal DESC
            ", [
                'village_id'   => $tps['village_id'] ?? '',
                'village_name' => $villageName,
                'rw_num'       => $rwNum
            ])->getResultArray();

            $dpraKegiatan = $db->query("
                SELECT k.id, k.nama_kegiatan, k.tanggal, k.lokasi_kegiatan as alamat, k.rt, k.rw, k.jumlah_peserta,
                       k.tokoh_hadir, k.catatan as keterangan, k.status_kondisi as status,
                       j.nama_kegiatan as bentuk_kegiatan, 'kegiatan_dpra' as sumber
                FROM kegiatan_list k
                LEFT JOIN kegiatan_jenis j ON k.jenis_kegiatan_id = j.id
                WHERE LOWER(TRIM(k.dpra)) LIKE CONCAT('%', LOWER(TRIM(:village_name:)), '%')
                  AND CAST(REGEXP_REPLACE(k.rw, '[^0-9]', '') AS UNSIGNED) = :rw_num:
                ORDER BY k.tanggal DESC
            ", [
                'village_name' => $villageName,
                'rw_num'       => $rwNum
            ])->getResultArray();

            $kegiatanList = array_merge($sisirKegiatan, $dpraKegiatan);
            usort($kegiatanList, function($a, $b) {
                return strcmp($b['tanggal'] ?? '', $a['tanggal'] ?? '');
            });
        }

        $totalPesertaKegiatan = array_sum(array_column($kegiatanList, 'jumlah_peserta'));

        return $this->response->setJSON([
            'status'          => 'success',
            'tps'             => $tps,
            'selected_caleg'  => $selectedCalegInfo,
            'top_caleg'       => $topCaleg,
            'top_party'       => $topParty,
            'stats'           => [
                'total_suara_sah'    => $totalSuaraSah,
                'total_suara_caleg'  => $totalSuaraCaleg,
                'total_suara_partai' => $totalSuaraPartai,
                'dpt'                => $dpt,
                'partisipasi'        => $partisipasi,
            ],
            'party_standings' => $partyStandings,
            'leaderboard'     => $leaderboard,
            'rw_data'         => [
                'rw'              => $tps['rw'] ?? '-',
                'kelurahan'       => $villageName,
                'total_anggota'   => count($members),
                'gender_l'        => $genderL,
                'gender_p'        => $genderP,
                'members'         => $members,
                'total_kegiatan'  => count($kegiatanList),
                'total_peserta'   => $totalPesertaKegiatan,
                'kegiatan'        => $kegiatanList,
            ]
        ]);
    }

    /**
     * AJAX endpoint to get detailed breakdown of party votes in a specific village:
     * 1. Suara coblos logo partai & suara masing-masing caleg
     * 2. Sebaran perolehan suara partai di seluruh RW (basis suara)
     */
    public function getPartyVillageBreakdown()
    {
        $db = \Config\Database::connect();
        $partaiId = $this->request->getGet('partai_id');
        $villageId = $this->request->getGet('village_id');
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';

        if (empty($partaiId) || empty($villageId)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'partai_id dan village_id diperlukan.'
            ]);
        }

        try {
            // 1. Info Partai
            $party = $db->query("
                SELECT p.id, p.nama_partai, p.lambang_partai, COALESCE(pe.no_urut, p.id) as no_urut
                FROM pemilu_partai p
                LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
                WHERE p.id = :partai_id:
            ", ['partai_id' => (int)$partaiId, 'tahun' => (int)$tahun])->getRowArray();

            if (!$party) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data partai tidak ditemukan.'
                ]);
            }

            // Acronym map
            $partyShortMap = [
                'Partai Keadilan Sejahtera' => 'PKS',
                'Partai Gerakan Indonesia Raya' => 'GERINDRA',
                'Partai Gerindra' => 'GERINDRA',
                'Partai Persatuan Pembangunan' => 'PPP',
                'Partai Golongan Karya' => 'GOLKAR',
                'Partai Golkar' => 'GOLKAR',
                'Partai Demokrasi Indonesia Perjuangan' => 'PDIP',
                'Partai Perindo' => 'PERINDO',
                'Partai Persatuan Indonesia' => 'PERINDO',
                'Partai Demokrat' => 'DEMOKRAT',
                'Partai Amanat Nasional' => 'PAN',
                'Partai Kebangkitan Bangsa' => 'PKB',
                'Partai Solidaritas Indonesia' => 'PSI',
                'Partai NasDem' => 'NASDEM',
                'Partai Nasdem' => 'NASDEM',
                'Partai Gelombang Rakyat Indonesia' => 'GELORA',
                'Partai Gelora' => 'GELORA',
                'Partai Buruh' => 'BURUH',
                'Partai Ummat' => 'UMMAT',
                'Partai Kebangkitan Nusantara' => 'PKN',
                'Partai Bulan Bintang' => 'PBB',
                'Partai Garda Perubahan Indonesia' => 'GARUDA',
                'Partai Garda Republik Indonesia' => 'GARUDA',
                'Partai Hati Nurani Rakyat' => 'HANURA',
            ];
            $acronym = $party['nama_partai'];
            foreach ($partyShortMap as $k => $v) {
                if (stripos($party['nama_partai'], $k) !== false) {
                    $acronym = $v;
                    break;
                }
            }
            $party['akronim'] = $acronym;

            // 2. Info Kelurahan
            $village = $db->table('reg_villages')->where('id', $villageId)->get()->getRowArray();
            $villageName = $village['name'] ?? 'Kelurahan';

            // 3. Suara Coblos Logo Partai di Kelurahan ini
            $stmtSp = $db->query("
                SELECT COALESCE(SUM(sp.jumlah_suara), 0) as suara_partai
                FROM pemilu_suara_partai sp
                JOIN pemilu_tps t ON sp.tps_id = t.id
                WHERE sp.partai_id = :partai_id: 
                  AND sp.tahun = :tahun: 
                  AND sp.tingkat_pemilu = :tingkat: 
                  AND t.village_id = :village_id:
            ", [
                'partai_id'  => (int)$partaiId,
                'tahun'      => (int)$tahun,
                'tingkat'    => $tingkat,
                'village_id' => $villageId
            ])->getRowArray();
            $suaraPartaiOnly = (int)($stmtSp['suara_partai'] ?? 0);

            // 4. Rincian Suara Seluruh Caleg Partai di Kelurahan ini
            $calegList = $db->query("
                SELECT 
                    c.id, c.no_urut, c.nama_caleg,
                    COALESCE(sc.jumlah_suara, 0) as suara_caleg
                FROM pemilu_caleg c
                LEFT JOIN (
                    SELECT sc_in.caleg_id, SUM(sc_in.jumlah_suara) as jumlah_suara
                    FROM pemilu_suara_caleg sc_in
                    JOIN pemilu_tps t ON sc_in.tps_id = t.id
                    WHERE t.village_id = :village_id: AND sc_in.tahun = :tahun:
                    GROUP BY sc_in.caleg_id
                ) sc ON sc.caleg_id = c.id
                WHERE c.partai_id = :partai_id: 
                  AND c.tahun = :tahun: 
                  AND c.tingkat_pemilu = :tingkat:
                ORDER BY c.no_urut ASC
            ", [
                'village_id' => $villageId,
                'tahun'      => (int)$tahun,
                'partai_id'  => (int)$partaiId,
                'tingkat'    => $tingkat
            ])->getResultArray();

        $totalSuaraCaleg = 0;
        foreach ($calegList as $c) {
            $totalSuaraCaleg += (int)$c['suara_caleg'];
        }
        $totalPartyVotes = $suaraPartaiOnly + $totalSuaraCaleg;

        foreach ($calegList as &$c) {
            $s = (int)$c['suara_caleg'];
            $c['pct_of_caleg'] = $totalSuaraCaleg > 0 ? round(($s / $totalSuaraCaleg) * 100, 1) : 0;
            $c['pct_of_total'] = $totalPartyVotes > 0 ? round(($s / $totalPartyVotes) * 100, 1) : 0;
        }
        unset($c);

        // 5. Rincian RW Basis Suara di Kelurahan ini (diurutkan perolehan tertinggi)
        $rwList = $db->query("
            SELECT 
                t.rw,
                COUNT(DISTINCT t.id) as tps_count,
                COALESCE(SUM(sp.jumlah_suara), 0) as suara_partai,
                COALESCE(SUM(sc.jumlah_suara), 0) as suara_caleg,
                (COALESCE(SUM(sp.jumlah_suara), 0) + COALESCE(SUM(sc.jumlah_suara), 0)) as total_suara
            FROM pemilu_tps t
            LEFT JOIN pemilu_suara_partai sp ON sp.tps_id = t.id 
                AND sp.partai_id = :partai_id: 
                AND sp.tahun = :tahun: 
                AND sp.tingkat_pemilu = :tingkat:
            LEFT JOIN (
                SELECT sc_in.tps_id, SUM(sc_in.jumlah_suara) as jumlah_suara
                FROM pemilu_suara_caleg sc_in
                JOIN pemilu_caleg c_in ON sc_in.caleg_id = c_in.id
                WHERE c_in.partai_id = :partai_id2: 
                  AND sc_in.tahun = :tahun2: 
                  AND c_in.tahun = :tahun3: 
                  AND c_in.tingkat_pemilu = :tingkat2:
                GROUP BY sc_in.tps_id
            ) sc ON sc.tps_id = t.id
            WHERE t.village_id = :village_id:
            GROUP BY t.rw
            ORDER BY total_suara DESC, CAST(t.rw AS UNSIGNED) ASC
        ", [
            'partai_id'  => (int)$partaiId,
            'tahun'      => (int)$tahun,
            'tingkat'    => $tingkat,
            'partai_id2' => (int)$partaiId,
            'tahun2'     => (int)$tahun,
            'tahun3'     => (int)$tahun,
            'tingkat2'   => $tingkat,
            'village_id' => $villageId
        ])->getResultArray();

        $maxRwSuara = count($rwList) > 0 ? (int)$rwList[0]['total_suara'] : 1;
        $rankRw = 1;
        foreach ($rwList as &$rw) {
            $rw['rank'] = $rankRw++;
            $tot = (int)$rw['total_suara'];
            $rw['pct_of_party'] = $totalPartyVotes > 0 ? round(($tot / $totalPartyVotes) * 100, 1) : 0;
            $rw['bar_percent'] = $maxRwSuara > 0 ? round(($tot / $maxRwSuara) * 100, 1) : 0;
        }
        unset($rw);

        // 6. Total Suara Sah Kelurahan (seluruh partai)
        $spKelurahan = (int)$db->table('pemilu_suara_partai sp')
            ->join('pemilu_tps t', 'sp.tps_id = t.id')
            ->where('sp.tahun', $tahun)
            ->where('sp.tingkat_pemilu', $tingkat)
            ->where('t.village_id', $villageId)
            ->selectSum('sp.jumlah_suara', 'total')
            ->get()->getRowArray()['total'] ?? 0;

        $scKelurahan = (int)$db->table('pemilu_suara_caleg sc')
            ->join('pemilu_caleg c', 'sc.caleg_id = c.id')
            ->join('pemilu_tps t', 'sc.tps_id = t.id')
            ->where('sc.tahun', $tahun)
            ->where('c.tahun', $tahun)
            ->where('c.tingkat_pemilu', $tingkat)
            ->where('t.village_id', $villageId)
            ->selectSum('sc.jumlah_suara', 'total')
            ->get()->getRowArray()['total'] ?? 0;

        $totalSuaraSahKelurahan = $spKelurahan + $scKelurahan;
        $partySharePercent = $totalSuaraSahKelurahan > 0 ? round(($totalPartyVotes / $totalSuaraSahKelurahan) * 100, 1) : 0;

        return $this->response->setJSON([
            'status'  => 'success',
            'party'   => $party,
            'village' => [
                'id'   => $villageId,
                'name' => $villageName
            ],
            'meta'    => [
                'tahun'          => $tahun,
                'tingkat_pemilu' => $tingkat
            ],
            'stats'   => [
                'total_suara'              => $totalPartyVotes,
                'suara_partai'             => $suaraPartaiOnly,
                'suara_caleg'              => $totalSuaraCaleg,
                'pct_suara_partai'         => $totalPartyVotes > 0 ? round(($suaraPartaiOnly / $totalPartyVotes) * 100, 1) : 0,
                'pct_suara_caleg'          => $totalPartyVotes > 0 ? round(($totalSuaraCaleg / $totalPartyVotes) * 100, 1) : 0,
                'total_suara_sah_kelurahan'=> $totalSuaraSahKelurahan,
                'party_share_percent'      => $partySharePercent
            ],
            'caleg_list' => $calegList,
            'rw_list'    => $rwList
        ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}

