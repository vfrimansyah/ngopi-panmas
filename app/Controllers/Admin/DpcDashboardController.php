<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DpcBidangModel;
use App\Models\DpcProkerModel;
use App\Models\DpcRabModel;
use App\Models\DpcMonevModel;

class DpcDashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $bidangModel = new DpcBidangModel();
        $prokerModel = new DpcProkerModel();
        $rabModel    = new DpcRabModel();
        $monevModel  = new DpcMonevModel();

        // 1. Metrics
        $totalProker     = $db->table('dpc_proker')->countAllResults();
        $prokerSelesai   = $db->table('dpc_proker')->where('status_monev', 'selesai')->countAllResults();
        $prokerBerjalan  = $db->table('dpc_proker')->where('status_monev', 'berjalan')->countAllResults();
        $prokerBelum     = $db->table('dpc_proker')->where('status_monev', 'belum')->countAllResults();
        $prokerTerkendala= $db->table('dpc_proker')->where('status_monev', 'terkendala')->countAllResults();

        $avgProgres = (float)($db->table('dpc_proker')->selectAvg('persentase_progres')->get()->getRow()->persentase_progres ?? 0);
        $totalAnggaran = (float)($db->table('dpc_rab')->selectSum('anggaran')->get()->getRow()->anggaran ?? 0);
        $totalRealisasi = (float)($db->table('dpc_rab')->selectSum('realisasi_anggaran')->get()->getRow()->realisasi_anggaran ?? 0);
        $persenAnggaran = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0;

        // 2. Chart Capaian Progres per Bidang (Bar Chart)
        $bidangs = $bidangModel->getWithStats();
        $bidangLabels = [];
        $bidangProgres = [];
        foreach ($bidangs as $b) {
            $bidangLabels[] = $b['kode'];
            $bidangProgres[] = $b['avg_progres'];
        }

        // 3. Chart Status Monev (Doughnut)
        $statusLabels = ['Selesai', 'Berjalan', 'Belum Mulai', 'Terkendala'];
        $statusValues = [$prokerSelesai, $prokerBerjalan, $prokerBelum, $prokerTerkendala];
        $statusColors = ['#10b981', '#3b82f6', '#94a3b8', '#ef4444'];

        // 4. Program Prioritas Tracking
        $prioritasList = $prokerModel->where('is_prioritas', 1)->orderBy('persentase_progres', 'DESC')->findAll();

        // 5. Recent Monev Logs
        $recentLogs = $monevModel->getRecentLogs(5);

        // 6. Summary RAB by Bidang
        $rabSummary = $rabModel->getSummaryByBidang();

        $perms = $this->getPagePermissions('dpc');

        $data = [
            'title'            => 'Dashboard & Monev DPC PKS',
            'totalProker'      => $totalProker,
            'prokerSelesai'    => $prokerSelesai,
            'prokerBerjalan'   => $prokerBerjalan,
            'prokerBelum'      => $prokerBelum,
            'prokerTerkendala' => $prokerTerkendala,
            'avgProgres'       => round($avgProgres, 1),
            'totalAnggaran'    => $totalAnggaran,
            'totalRealisasi'   => $totalRealisasi,
            'persenAnggaran'   => $persenAnggaran,
            'bidangLabels'     => json_encode($bidangLabels),
            'bidangProgres'    => json_encode($bidangProgres),
            'statusLabels'     => json_encode($statusLabels),
            'statusValues'     => json_encode($statusValues),
            'statusColors'     => json_encode($statusColors),
            'bidangs'          => $bidangs,
            'prioritasList'    => $prioritasList,
            'recentLogs'       => $recentLogs,
            'rabSummary'       => $rabSummary,
            'can_create'       => $perms['canCreate'],
            'can_update'       => $perms['canUpdate'],
            'can_delete'       => $perms['canDelete'],
        ];

        return view('admin/dpc/dashboard', $data);
    }
}
