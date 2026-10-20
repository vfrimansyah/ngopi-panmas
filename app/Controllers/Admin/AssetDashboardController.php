<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AssetDashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Metrics
        $totalJenis  = $db->table('assets')->countAllResults();
        $totalUnit   = $db->table('assets')->selectSum('jumlah')->get()->getRow()->jumlah ?? 0;
        $totalBaik   = $db->table('assets')->selectSum('jumlah')->where('kondisi', 'baik')->get()->getRow()->jumlah ?? 0;
        $totalRusak  = $db->table('assets')->selectSum('jumlah')->whereIn('kondisi', ['rusak', 'kurang_baik'])->get()->getRow()->jumlah ?? 0;

        // Chart: Distribusi per Lokasi
        $lokasiRows = $db->table('assets')
            ->select('lokasi, SUM(jumlah) as total')
            ->groupBy('lokasi')
            ->orderBy('lokasi', 'ASC')
            ->get()->getResultArray();
        $lokasiLabels = array_column($lokasiRows, 'lokasi');
        $lokasiValues = array_map('intval', array_column($lokasiRows, 'total'));

        // Chart: Distribusi per Kondisi
        $kondisiRows = $db->table('assets')
            ->select("kondisi, SUM(jumlah) as total")
            ->groupBy('kondisi')
            ->get()->getResultArray();
        $kondisiLabels = [];
        $kondisiValues = [];
        $kondisiColors = [];
        $kondisiMap = [
            'baik'        => ['label' => 'Baik',        'color' => '#10b981'],
            'kurang_baik' => ['label' => 'Kurang Baik', 'color' => '#f59e0b'],
            'rusak'       => ['label' => 'Rusak',       'color' => '#ef4444'],
            'hilang'      => ['label' => 'Hilang',      'color' => '#6b7280'],
        ];
        foreach ($kondisiRows as $row) {
            $key = $row['kondisi'];
            $kondisiLabels[] = $kondisiMap[$key]['label'] ?? ucfirst($key);
            $kondisiValues[] = intval($row['total']);
            $kondisiColors[] = $kondisiMap[$key]['color'] ?? '#94a3b8';
        }

        // Recent Assets
        $recentAssets = $db->table('assets')
            ->orderBy('updated_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $perms = $this->getPagePermissions('aset');

        $data = [
            'title'         => 'Dashboard Aset & Inventaris',
            'totalJenis'    => $totalJenis,
            'totalUnit'     => $totalUnit,
            'totalBaik'     => $totalBaik,
            'totalRusak'    => $totalRusak,
            'lokasiLabels'  => json_encode($lokasiLabels),
            'lokasiValues'  => json_encode($lokasiValues),
            'kondisiLabels' => json_encode($kondisiLabels),
            'kondisiValues' => json_encode($kondisiValues),
            'kondisiColors' => json_encode($kondisiColors),
            'recentAssets'  => $recentAssets,
            'can_create'    => $perms['canCreate'],
            'can_update'    => $perms['canUpdate'],
            'can_delete'    => $perms['canDelete'],
        ];

        return view('admin/aset/dashboard', $data);
    }
}
