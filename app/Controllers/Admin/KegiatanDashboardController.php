<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KegiatanListModel;
use App\Models\KegiatanJenisModel;

class KegiatanDashboardController extends BaseController
{
    protected $kegiatanModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanListModel();
        $this->jenisModel    = new KegiatanJenisModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Total Jumlah Kegiatan & Status Kondisi
        $totalKegiatan = $db->table('kegiatan_list')->countAllResults();
        $totalHijau    = $db->table('kegiatan_list')->where('status_kondisi', 'hijau')->countAllResults();
        $totalKuning   = $db->table('kegiatan_list')->where('status_kondisi', 'kuning')->countAllResults();
        $totalMerah    = $db->table('kegiatan_list')->where('status_kondisi', 'merah')->countAllResults();

        // 2. Tabel Kegiatan Terakhir (Top 5)
        $recentInput = $db->table('kegiatan_list')
            ->select('penyelenggara, updated_at, nama_kegiatan')
            ->orderBy('updated_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        // 3. List DPRa aktif dari MasterDpraModel
        $dpraRows = (new \App\Models\MasterDpraModel())->where('status', 'aktif')->orderBy('nama_dpra', 'ASC')->findAll();
        $dpraList = [];
        foreach ($dpraRows as $dr) {
            $dpraList[$dr['nama_dpra']] = $dr['nama_dpra'];
        }

        // Chart 1: Grafik Kegiatan DPRa (Penyelenggara = DPRa)
        $dpraStats = [];
        foreach ($dpraList as $name => $lbl) {
            $count = $db->table('kegiatan_list')
                ->where('penyelenggara', $name)
                ->countAllResults();
            $dpraStats[$name] = $count;
        }

        // Chart 2: Grafik Lokasi Kegiatan (dpra column)
        $lokasiStats = [];
        foreach ($dpraList as $name => $lbl) {
            $count = $db->table('kegiatan_list')
                ->where('dpra', $name)
                ->countAllResults();
            $lokasiStats[$name] = $count;
        }

        // Chart 3: Grafik Kegiatan Sie DPC (Penyelenggara non-DPRa)
        $sieList = [
            'BPH', 'Bipeka', 'BKAP', 'Bid Kepemudaan', 'BPUD',
            'Bid Komdigi', 'BKO', 'Bid Kessos', 'BPJE', 'BP3'
        ];
        $sieStats = [];
        foreach ($sieList as $sie) {
            $count = $db->table('kegiatan_list')
                ->like('penyelenggara', $sie)
                ->countAllResults();
            $sieStats[$sie] = $count;
        }

        // Chart 4: Grafik Jenis Kegiatan (Count per Jenis)
        $allJenis = $this->jenisModel->orderBy('nomor_urut', 'ASC')->findAll();
        $jenisStats = [];
        foreach ($allJenis as $j) {
            $count = $db->table('kegiatan_list')
                ->where('jenis_kegiatan_id', $j['id'])
                ->countAllResults();
            $jenisStats[$j['nama_kegiatan']] = $count;
        }

        $perms = $this->getPagePermissions('kegiatan');

        $data = [
            'title'         => 'Dashboard Kegiatan',
            'totalKegiatan' => $totalKegiatan,
            'totalHijau'    => $totalHijau,
            'totalKuning'   => $totalKuning,
            'totalMerah'    => $totalMerah,
            'recentInput'   => $recentInput,
            'dpraStats'     => $dpraStats,
            'lokasiStats'   => $lokasiStats,
            'sieStats'      => $sieStats,
            'jenisStats'    => $jenisStats,
            'can_create'    => $perms['canCreate'],
            'can_update'    => $perms['canUpdate'],
            'can_delete'    => $perms['canDelete'],
        ];

        return view('admin/kegiatan/dashboard', $data);
    }
}
