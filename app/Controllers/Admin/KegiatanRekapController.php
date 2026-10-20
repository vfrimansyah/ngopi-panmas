<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class KegiatanRekapController extends BaseController
{
    // Mapping 6 DPRa dan jumlah total RW resmi di masing-masing kelurahan Pancoran Mas
    private $dpraRwConfig = [
        'DPRa Depok'               => ['name' => 'DPRa Depok',               'kelurahan' => 'Depok',                'total_rw' => 23],
        'DPRa Depok Jaya'          => ['name' => 'DPRa Depok Jaya',          'kelurahan' => 'Depok Jaya',           'total_rw' => 14],
        'DPRa Pancoran Mas'        => ['name' => 'DPRa Pancoran Mas',        'kelurahan' => 'Pancoran Mas',         'total_rw' => 21],
        'DPRa Mampang'             => ['name' => 'DPRa Mampang',             'kelurahan' => 'Mampang',              'total_rw' => 16],
        'DPRa Rangkapan Jaya'      => ['name' => 'DPRa Rangkapan Jaya',      'kelurahan' => 'Rangkapan Jaya',       'total_rw' => 19],
        'DPRa Rangkapan Jaya Baru' => ['name' => 'DPRa Rangkapan Jaya Baru', 'kelurahan' => 'Rangkapan Jaya Baru',  'total_rw' => 15],
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // REKAP PER DPRA
    // ─────────────────────────────────────────────────────────────────────────
    public function rekapDpra()
    {
        $db = \Config\Database::connect();

        $fromDate = $this->request->getGet('from_date');
        $toDate   = $this->request->getGet('to_date');

        $rekapRows    = [];
        $grandTotalRw = 0;
        $grandTotalKegiatan = 0;

        foreach ($this->dpraRwConfig as $label => $cfg) {
            $totalRw = $cfg['total_rw'];
            $grandTotalRw += $totalRw;

            // Fetch activity count per RW for this DPRa
            $builder = $db->table('kegiatan_list')
                ->select('rw, COUNT(*) as cnt')
                ->groupStart()
                    ->where('dpra', $cfg['name'])
                    ->orWhere('dpra', $cfg['kelurahan'])
                    ->orWhere('penyelenggara', $cfg['name'])
                ->groupEnd();

            if ($fromDate) $builder->where('tanggal >=', $fromDate);
            if ($toDate)   $builder->where('tanggal <=', $toDate);

            $rows = $builder->groupBy('rw')->get()->getResultArray();

            $rwMap = [];
            $totalKegiatanDpra = 0;
            foreach ($rows as $r) {
                if (!empty($r['rw'])) {
                    $rwMap[$r['rw']] = intval($r['cnt']);
                    $totalKegiatanDpra += intval($r['cnt']);
                }
            }

            $grandTotalKegiatan += $totalKegiatanDpra;
            $rataPerRw = $totalRw > 0 ? round($totalKegiatanDpra / $totalRw, 2) : 0;

            // Categorize each RW 1..total_rw
            $blankspotCount = 0;
            $minimCount     = 0; // 1-5
            $diAtas5Count   = 0; // >5

            for ($rwNum = 1; $rwNum <= $totalRw; $rwNum++) {
                $count = $rwMap[(string)$rwNum] ?? ($rwMap[str_pad($rwNum, 2, '0', STR_PAD_LEFT)] ?? 0);
                if ($count == 0) {
                    $blankspotCount++;
                } elseif ($count >= 1 && $count <= 5) {
                    $minimCount++;
                } else {
                    $diAtas5Count++;
                }
            }

            $pctBlankspot = $totalRw > 0 ? round(($blankspotCount / $totalRw) * 100, 1) : 0;
            $pctMinim     = $totalRw > 0 ? round(($minimCount / $totalRw) * 100, 1) : 0;
            $pctDiAtas5   = $totalRw > 0 ? round(($diAtas5Count / $totalRw) * 100, 1) : 0;

            $rekapRows[] = [
                'dpra'           => $cfg['name'],
                'kelurahan'      => $cfg['kelurahan'],
                'jml_rw'         => $totalRw,
                'jml_kegiatan'   => $totalKegiatanDpra,
                'rata_per_rw'    => $rataPerRw,
                'blankspot'      => $blankspotCount,
                'pct_blankspot'  => $pctBlankspot,
                'minim'          => $minimCount,
                'pct_minim'      => $pctMinim,
                'di_atas_5'      => $diAtas5Count,
                'pct_di_atas_5'  => $pctDiAtas5,
            ];
        }

        $rataRataTotal = $grandTotalRw > 0 ? round($grandTotalKegiatan / $grandTotalRw, 2) : 0;

        $perms = $this->getPagePermissions('kegiatan');

        $data = [
            'title'              => 'Rekap per DPRa',
            'rekapRows'          => $rekapRows,
            'grandTotalRw'       => $grandTotalRw,
            'grandTotalKegiatan' => $grandTotalKegiatan,
            'rataRataTotal'      => $rataRataTotal,
            'fromDate'           => $fromDate,
            'toDate'             => $toDate,
            'can_create'         => $perms['canCreate'],
            'can_update'         => $perms['canUpdate'],
            'can_delete'         => $perms['canDelete'],
        ];

        return view('admin/kegiatan/rekap_dpra', $data);
    }

    /**
     * Export Excel Rekap DPRa
     */
    public function exportDpra()
    {
        $db = \Config\Database::connect();
        $fromDate = $this->request->getGet('from_date');
        $toDate   = $this->request->getGet('to_date');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Rekap_Kegiatan_DPRa_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>DPRa</th>
                <th>Jml RW</th>
                <th>Jml Kegiatan</th>
                <th>Rata2/RW</th>
                <th>Blankspot</th>
                <th>% Blankspot</th>
                <th>Minim (1-5)</th>
                <th>% Minim</th>
                <th>Di atas 5</th>
                <th>% Di atas 5</th>
              </tr>";

        $no = 1;
        foreach ($this->dpraRwConfig as $label => $cfg) {
            $totalRw = $cfg['total_rw'];
            $builder = $db->table('kegiatan_list')
                ->select('rw, COUNT(*) as cnt')
                ->groupStart()
                    ->where('dpra', $cfg['name'])
                    ->orWhere('dpra', $cfg['kelurahan'])
                    ->orWhere('penyelenggara', $cfg['name'])
                ->groupEnd();

            if ($fromDate) $builder->where('tanggal >=', $fromDate);
            if ($toDate)   $builder->where('tanggal <=', $toDate);

            $rows = $builder->groupBy('rw')->get()->getResultArray();
            $rwMap = [];
            $totalKegiatanDpra = 0;
            foreach ($rows as $r) {
                if (!empty($r['rw'])) {
                    $rwMap[$r['rw']] = intval($r['cnt']);
                    $totalKegiatanDpra += intval($r['cnt']);
                }
            }

            $blankspotCount = 0;
            $minimCount = 0;
            $diAtas5Count = 0;

            for ($rwNum = 1; $rwNum <= $totalRw; $rwNum++) {
                $count = $rwMap[(string)$rwNum] ?? ($rwMap[str_pad($rwNum, 2, '0', STR_PAD_LEFT)] ?? 0);
                if ($count == 0) $blankspotCount++;
                elseif ($count <= 5) $minimCount++;
                else $diAtas5Count++;
            }

            $rataPerRw = $totalRw > 0 ? round($totalKegiatanDpra / $totalRw, 2) : 0;
            $pctB = $totalRw > 0 ? round(($blankspotCount / $totalRw) * 100, 1) : 0;
            $pctM = $totalRw > 0 ? round(($minimCount / $totalRw) * 100, 1) : 0;
            $pctD = $totalRw > 0 ? round(($diAtas5Count / $totalRw) * 100, 1) : 0;

            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . esc($cfg['name']) . " (" . esc($cfg['kelurahan']) . ")</td>
                    <td>" . $totalRw . "</td>
                    <td>" . $totalKegiatanDpra . "</td>
                    <td>" . $rataPerRw . "</td>
                    <td>" . $blankspotCount . "</td>
                    <td>" . $pctB . "%</td>
                    <td>" . $minimCount . "</td>
                    <td>" . $pctM . "%</td>
                    <td>" . $diAtas5Count . "</td>
                    <td>" . $pctD . "%</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // REKAP PER RW
    // ─────────────────────────────────────────────────────────────────────────
    public function rekapRw()
    {
        $db = \Config\Database::connect();

        $fromDate    = $this->request->getGet('from_date');
        $toDate      = $this->request->getGet('to_date');
        $selectedDpra = $this->request->getGet('dpra');

        $rekapRwRows = [];

        foreach ($this->dpraRwConfig as $label => $cfg) {
            if (!empty($selectedDpra) && $selectedDpra !== $cfg['name'] && $selectedDpra !== $cfg['kelurahan']) {
                continue;
            }

            $totalRw = $cfg['total_rw'];

            $builder = $db->table('kegiatan_list')
                ->select('rw, COUNT(*) as cnt')
                ->groupStart()
                    ->where('dpra', $cfg['name'])
                    ->orWhere('dpra', $cfg['kelurahan'])
                    ->orWhere('penyelenggara', $cfg['name'])
                ->groupEnd();

            if ($fromDate) $builder->where('tanggal >=', $fromDate);
            if ($toDate)   $builder->where('tanggal <=', $toDate);

            $rows = $builder->groupBy('rw')->get()->getResultArray();

            $rwCountMap = [];
            foreach ($rows as $r) {
                if (!empty($r['rw'])) {
                    $rwCountMap[intval($r['rw'])] = intval($r['cnt']);
                }
            }

            for ($rwNum = 1; $rwNum <= $totalRw; $rwNum++) {
                $cnt = $rwCountMap[$rwNum] ?? 0;
                $rekapRwRows[] = [
                    'dpra'         => $cfg['name'],
                    'kelurahan'    => $cfg['kelurahan'],
                    'rw'           => $rwNum,
                    'jml_kegiatan' => $cnt,
                ];
            }
        }

        $perms = $this->getPagePermissions('kegiatan');

        $data = [
            'title'        => 'Rekap per RW',
            'rekapRwRows'  => $rekapRwRows,
            'fromDate'     => $fromDate,
            'toDate'       => $toDate,
            'selectedDpra' => $selectedDpra,
            'dpraList'     => $this->dpraRwConfig,
            'can_create'   => $perms['canCreate'],
            'can_update'   => $perms['canUpdate'],
            'can_delete'   => $perms['canDelete'],
        ];

        return view('admin/kegiatan/rekap_rw', $data);
    }

    /**
     * Export Excel Rekap RW
     */
    public function exportRw()
    {
        $db = \Config\Database::connect();
        $fromDate     = $this->request->getGet('from_date');
        $toDate       = $this->request->getGet('to_date');
        $selectedDpra = $this->request->getGet('dpra');

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Rekap_Kegiatan_RW_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>Kelurahan (Lokasi Kegiatan)</th>
                <th>RW</th>
                <th>Jml Kegiatan</th>
              </tr>";

        foreach ($this->dpraRwConfig as $label => $cfg) {
            if (!empty($selectedDpra) && $selectedDpra !== $cfg['name'] && $selectedDpra !== $cfg['kelurahan']) {
                continue;
            }

            $totalRw = $cfg['total_rw'];

            $builder = $db->table('kegiatan_list')
                ->select('rw, COUNT(*) as cnt')
                ->groupStart()
                    ->where('dpra', $cfg['name'])
                    ->orWhere('dpra', $cfg['kelurahan'])
                    ->orWhere('penyelenggara', $cfg['name'])
                ->groupEnd();

            if ($fromDate) $builder->where('tanggal >=', $fromDate);
            if ($toDate)   $builder->where('tanggal <=', $toDate);

            $rows = $builder->groupBy('rw')->get()->getResultArray();
            $rwCountMap = [];
            foreach ($rows as $r) {
                if (!empty($r['rw'])) {
                    $rwCountMap[intval($r['rw'])] = intval($r['cnt']);
                }
            }

            for ($rwNum = 1; $rwNum <= $totalRw; $rwNum++) {
                $cnt = $rwCountMap[$rwNum] ?? 0;
                echo "<tr>
                        <td>" . esc($cfg['name']) . " (" . esc($cfg['kelurahan']) . ")</td>
                        <td>" . $rwNum . "</td>
                        <td>" . $cnt . "</td>
                      </tr>";
            }
        }
        echo "</table>";
        exit;
    }
}
