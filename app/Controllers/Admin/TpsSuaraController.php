<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class TpsSuaraController extends BaseController
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

        // Get districts
        $districts = $db->table('reg_districts')
            ->where('regency_id', '3276')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get villages
        $villages = $db->table('reg_villages')
            ->where('district_id', $selectedDistrict)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Determine default village
        $selectedVillage = $this->request->getGet('village_id');
        if (empty($selectedVillage) && !empty($villages)) {
            $selectedVillage = $villages[0]['id'];
        }

        // Fetch PKS Party ID
        $pksParty = $db->table('pemilu_partai')
            ->where('nama_partai LIKE', '%Keadilan Sejahtera%')
            ->orWhere('nama_partai LIKE', '%PKS%')
            ->get()
            ->getRowArray();
        $pksId = $pksParty['id'] ?? 8;

        $rwResults = [];
        $tpsResults = [];

        if (!empty($selectedVillage)) {
            // 1. RW Summary Calculations
            $rwQuery = $db->query("
                SELECT 
                    t.rw,
                    COUNT(DISTINCT t.id) as total_tps,
                    SUM(COALESCE(st.suara_sah, 0)) as total_suara_sah,
                    SUM(COALESCE(st.suara_tidak_sah, 0)) as total_suara_tidak_sah,
                    SUM(COALESCE(st.suara_total, 0)) as total_suara_total,
                    SUM(COALESCE(v.pks_partai, 0) + COALESCE(v.pks_caleg, 0)) as total_suara_pks
                FROM pemilu_tps t
                LEFT JOIN pemilu_suara_tps st ON st.tps_id = t.id AND st.tahun = :tahun: AND st.tingkat_pemilu = :tingkat:
                LEFT JOIN (
                    SELECT 
                        tps_id,
                        SUM(CASE WHEN type = 'partai' THEN jumlah_suara ELSE 0 END) as pks_partai,
                        SUM(CASE WHEN type = 'caleg' THEN jumlah_suara ELSE 0 END) as pks_caleg
                    FROM (
                        SELECT tps_id, jumlah_suara, 'partai' as type 
                        FROM pemilu_suara_partai 
                        WHERE tahun = :tahun: AND partai_id = :pks_id: AND tingkat_pemilu = :tingkat:
                        
                        UNION ALL
                        
                        SELECT sc.tps_id, sc.jumlah_suara, 'caleg' as type 
                        FROM pemilu_suara_caleg sc
                        JOIN pemilu_caleg c ON sc.caleg_id = c.id
                        WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.partai_id = :pks_id: AND c.tingkat_pemilu = :tingkat:
                    ) inner_v
                    GROUP BY tps_id
                ) v ON v.tps_id = t.id
                WHERE t.village_id = :village_id:
                GROUP BY t.rw
                ORDER BY t.rw ASC
            ", [
                'tahun' => $selectedYear,
                'pks_id' => $pksId,
                'village_id' => $selectedVillage,
                'tingkat' => $selectedTingkat
            ]);
            $rwResults = $rwQuery->getResultArray();

            // 2. TPS Detailed Votes
            $tpsQuery = $db->query("
                SELECT 
                    t.id,
                    t.tps,
                    t.rw,
                    t.rt,
                    COALESCE(st.suara_sah, 0) as suara_sah,
                    COALESCE(st.suara_tidak_sah, 0) as suara_tidak_sah,
                    COALESCE(st.suara_total, 0) as suara_total,
                    COALESCE(vp.suara_partai, 0) as pks_partai,
                    COALESCE(vc.suara_caleg, 0) as pks_caleg,
                    (COALESCE(vp.suara_partai, 0) + COALESCE(vc.suara_caleg, 0)) as pks_total
                FROM pemilu_tps t
                LEFT JOIN pemilu_suara_tps st ON st.tps_id = t.id AND st.tahun = :tahun: AND st.tingkat_pemilu = :tingkat:
                LEFT JOIN (
                    SELECT tps_id, SUM(jumlah_suara) as suara_partai
                    FROM pemilu_suara_partai
                    WHERE tahun = :tahun: AND partai_id = :pks_id: AND tingkat_pemilu = :tingkat:
                    GROUP BY tps_id
                ) vp ON vp.tps_id = t.id
                LEFT JOIN (
                    SELECT sc.tps_id, SUM(sc.jumlah_suara) as suara_caleg
                    FROM pemilu_suara_caleg sc
                    JOIN pemilu_caleg c ON sc.caleg_id = c.id
                    WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.partai_id = :pks_id: AND c.tingkat_pemilu = :tingkat:
                    GROUP BY sc.tps_id
                ) vc ON vc.tps_id = t.id
                WHERE t.village_id = :village_id:
                ORDER BY t.tps ASC
            ", [
                'tahun' => $selectedYear,
                'pks_id' => $pksId,
                'village_id' => $selectedVillage,
                'tingkat' => $selectedTingkat
            ]);
            $tpsResults = $tpsQuery->getResultArray();
        }

        $data = [
            'title'            => 'Data Suara per TPS & RW',
            'years'            => $years,
            'selectedYear'     => $selectedYear,
            'selectedTingkat'  => $selectedTingkat,
            'tingkatOptions'   => [
                'DPRD_KOTA' => 'DPRD Kota Depok (Dapil 1)',
                'DPRD_PROV' => 'DPRD Provinsi Jawa Barat (Dapil 8)',
                'DPR_RI'    => 'DPR RI (Dapil Jabar 6: Depok - Bekasi)',
            ],
            'districts'        => $districts,
            'selectedDistrict' => $selectedDistrict,
            'villages'         => $villages,
            'selectedVillage'  => $selectedVillage,
            'rwResults'        => $rwResults,
            'tpsResults'       => $tpsResults
        ];

        return view('admin/pemilu_tps_suara/index', $data);
    }

    public function getRwDetail()
    {
        $db = \Config\Database::connect();
        $rw = $this->request->getGet('rw');
        $villageId = $this->request->getGet('village_id');
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';

        if (empty($rw) || empty($villageId)) {
            return $this->response->setJSON(['error' => 'Invalid parameters']);
        }

        // 1. Get total votes (party + caleg) for ALL parties in this RW
        $partyVotesQuery = $db->query("
            SELECT 
                p.id,
                p.nama_partai,
                p.lambang_partai,
                pe.no_urut,
                SUM(COALESCE(sp.jumlah_suara, 0)) as suara_partai
            FROM pemilu_partai p
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            LEFT JOIN pemilu_suara_partai sp ON sp.partai_id = p.id 
                AND sp.tahun = :tahun: 
                AND sp.tingkat_pemilu = :tingkat:
                AND sp.tps_id IN (SELECT id FROM pemilu_tps WHERE village_id = :village_id: AND rw = :rw:)
            WHERE sp.id IS NOT NULL OR pe.id IS NOT NULL
            GROUP BY p.id, p.nama_partai, p.lambang_partai, pe.no_urut
        ", ['tahun' => $tahun, 'village_id' => $villageId, 'rw' => $rw, 'tingkat' => $tingkat])->getResultArray();

        $calegVotesQuery = $db->query("
            SELECT 
                c.partai_id,
                SUM(sc.jumlah_suara) as suara_caleg
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            JOIN pemilu_tps t ON sc.tps_id = t.id
            WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat: AND t.village_id = :village_id: AND t.rw = :rw:
            GROUP BY c.partai_id
        ", ['tahun' => $tahun, 'village_id' => $villageId, 'rw' => $rw, 'tingkat' => $tingkat])->getResultArray();

        $calegMap = [];
        foreach ($calegVotesQuery as $r) {
            $calegMap[$r['partai_id']] = intval($r['suara_caleg']);
        }

        $partyResults = [];
        $totalValid = 0;
        foreach ($partyVotesQuery as $p) {
            $suaraPartai = intval($p['suara_partai']);
            $suaraCaleg = $calegMap[$p['id']] ?? 0;
            $total = $suaraPartai + $suaraCaleg;
            $totalValid += $total;

            $partyResults[] = [
                'no_urut'        => $p['no_urut'] ?: '-',
                'nama_partai'    => $p['nama_partai'],
                'lambang_partai' => $p['lambang_partai'],
                'suara_partai'   => $suaraPartai,
                'suara_caleg'    => $suaraCaleg,
                'total_suara'    => $total
            ];
        }

        usort($partyResults, function($a, $b) {
            return $b['total_suara'] - $a['total_suara'];
        });

        // 2. Get Top 5 Candidates in this RW
        $candidates = $db->query("
            SELECT 
                c.no_urut,
                c.nama_caleg,
                p.nama_partai,
                p.lambang_partai,
                SUM(sc.jumlah_suara) as total_suara
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            JOIN pemilu_partai p ON c.partai_id = p.id
            JOIN pemilu_tps t ON sc.tps_id = t.id
            WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat: 
              AND t.village_id = :village_id: AND t.rw = :rw:
            GROUP BY c.id, c.no_urut, c.nama_caleg, p.nama_partai, p.lambang_partai
            ORDER BY total_suara DESC
            LIMIT 5
        ", ['tahun' => $tahun, 'village_id' => $villageId, 'rw' => $rw, 'tingkat' => $tingkat])->getResultArray();

        return $this->response->setJSON([
            'parties'      => $partyResults,
            'total_valid'  => $totalValid,
            'candidates'   => $candidates
        ]);
    }

    public function getTpsDetail()
    {
        $db = \Config\Database::connect();
        $tpsId = $this->request->getGet('tps_id');
        $tahun = $this->request->getGet('tahun') ?: '2024';
        $tingkat = $this->request->getGet('tingkat_pemilu') ?: 'DPRD_KOTA';

        if (empty($tpsId)) {
            return $this->response->setJSON(['error' => 'Invalid parameters']);
        }

        // 1. Get total votes for ALL parties in this TPS
        $partyVotesQuery = $db->query("
            SELECT 
                p.id,
                p.nama_partai,
                p.lambang_partai,
                pe.no_urut,
                SUM(COALESCE(sp.jumlah_suara, 0)) as suara_partai
            FROM pemilu_partai p
            LEFT JOIN pemilu_peserta pe ON pe.partai_id = p.id AND pe.tahun = :tahun:
            LEFT JOIN pemilu_suara_partai sp ON sp.partai_id = p.id AND sp.tahun = :tahun: AND sp.tingkat_pemilu = :tingkat: AND sp.tps_id = :tps_id:
            WHERE sp.id IS NOT NULL OR pe.id IS NOT NULL
            GROUP BY p.id, p.nama_partai, p.lambang_partai, pe.no_urut
        ", ['tahun' => $tahun, 'tps_id' => $tpsId, 'tingkat' => $tingkat])->getResultArray();

        $calegVotesQuery = $db->query("
            SELECT 
                c.partai_id,
                SUM(sc.jumlah_suara) as suara_caleg
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat: AND sc.tps_id = :tps_id:
            GROUP BY c.partai_id
        ", ['tahun' => $tahun, 'tps_id' => $tpsId, 'tingkat' => $tingkat])->getResultArray();

        $calegMap = [];
        foreach ($calegVotesQuery as $r) {
            $calegMap[$r['partai_id']] = intval($r['suara_caleg']);
        }

        $partyResults = [];
        $totalValid = 0;
        foreach ($partyVotesQuery as $p) {
            $suaraPartai = intval($p['suara_partai']);
            $suaraCaleg = $calegMap[$p['id']] ?? 0;
            $total = $suaraPartai + $suaraCaleg;
            $totalValid += $total;

            $partyResults[] = [
                'no_urut'        => $p['no_urut'] ?: '-',
                'nama_partai'    => $p['nama_partai'],
                'lambang_partai' => $p['lambang_partai'],
                'suara_partai'   => $suaraPartai,
                'suara_caleg'    => $suaraCaleg,
                'total_suara'    => $total
            ];
        }

        usort($partyResults, function($a, $b) {
            return $b['total_suara'] - $a['total_suara'];
        });

        // 2. Get Top 5 Candidates in this TPS
        $candidates = $db->query("
            SELECT 
                c.no_urut,
                c.nama_caleg,
                p.nama_partai,
                p.lambang_partai,
                SUM(sc.jumlah_suara) as total_suara
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            JOIN pemilu_partai p ON c.partai_id = p.id
            WHERE sc.tahun = :tahun: AND c.tahun = :tahun: AND c.tingkat_pemilu = :tingkat: AND sc.tps_id = :tps_id:
            GROUP BY c.id, c.no_urut, c.nama_caleg, p.nama_partai, p.lambang_partai
            ORDER BY total_suara DESC
            LIMIT 5
        ", ['tahun' => $tahun, 'tps_id' => $tpsId, 'tingkat' => $tingkat])->getResultArray();

        return $this->response->setJSON([
            'parties'     => $partyResults,
            'total_valid' => $totalValid,
            'candidates'  => $candidates
        ]);
    }
}
