<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DpcBidangModel;
use App\Models\DpcProkerModel;
use App\Models\DpcMonevModel;

class DpcMonevController extends BaseController
{
    protected $monevModel;
    protected $prokerModel;
    protected $bidangModel;

    public function __construct()
    {
        $this->monevModel  = new DpcMonevModel();
        $this->prokerModel = new DpcProkerModel();
        $this->bidangModel = new DpcBidangModel();
    }

    public function index()
    {
        $bidangKode = $this->request->getGet('bidang');
        $status     = $this->request->getGet('status');
        $periode    = $this->request->getGet('periode') ?: 'Semua Periode';

        $filters = [
            'bidang_kode'  => $bidangKode,
            'status_monev' => $status,
            'search'       => $this->request->getGet('search'),
        ];

        $prokers = $this->prokerModel->getList($filters);
        $bidangs = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();
        $logs    = $this->monevModel->getRecentLogs(30);

        // Group logs by proker_id for quick display
        $logsByProker = [];
        foreach ($logs as $l) {
            $logsByProker[$l['proker_id']][] = $l;
        }

        $perms = $this->getPagePermissions('dpc');

        $data = [
            'title'        => 'Tools Monitoring & Evaluasi (Monev) DPC',
            'prokers'      => $prokers,
            'bidangs'      => $bidangs,
            'logs'         => $logs,
            'logsByProker' => $logsByProker,
            'filters'      => $filters,
            'can_create'   => $perms['canCreate'],
            'can_update'   => $perms['canUpdate'],
            'can_delete'   => $perms['canDelete'],
        ];

        return view('admin/dpc/monev/index', $data);
    }

    public function log(int $prokerId)
    {
        $proker = $this->prokerModel->find($prokerId);
        if (!$proker) {
            return redirect()->to('admin/dpc/monev')->with('error', 'Program kerja tidak ditemukan.');
        }

        $logs = $this->monevModel->getLogsByProker($prokerId);

        $data = [
            'title'  => 'Riwayat Monev: ' . $proker['nama_program'],
            'proker' => $proker,
            'logs'   => $logs,
        ];

        return view('admin/dpc/monev/log', $data);
    }

    public function saveLog()
    {
        $rules = [
            'proker_id'          => 'required|is_natural_no_zero',
            'periode_evaluasi'   => 'required',
            'tanggal_evaluasi'   => 'required|valid_date',
            'persentase_progres' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $prokerId = (int)$this->request->getPost('proker_id');
        $progres  = (int)$this->request->getPost('persentase_progres');
        $status   = $this->request->getPost('status_monev');

        // Auto determine status if not explicitly passed
        if (empty($status)) {
            if ($progres >= 100) $status = 'selesai';
            elseif ($progres > 0) $status = 'berjalan';
            else $status = 'belum';
        }

        // 1. Insert Monev Log
        $this->monevModel->save([
            'proker_id'          => $prokerId,
            'periode_evaluasi'   => $this->request->getPost('periode_evaluasi'),
            'tanggal_evaluasi'   => $this->request->getPost('tanggal_evaluasi'),
            'persentase_progres' => $progres,
            'capaian_kpi'        => $this->request->getPost('capaian_kpi'),
            'kendala'            => $this->request->getPost('kendala'),
            'tindak_lanjut'      => $this->request->getPost('tindak_lanjut'),
            'evaluator_nama'     => $this->request->getPost('evaluator_nama') ?: (auth()->user()->username ?? 'Admin'),
        ]);

        // 2. Update Proker Progress & Status
        $this->prokerModel->update($prokerId, [
            'persentase_progres' => $progres,
            'status_monev'       => $status,
            'realisasi_capaian'  => $this->request->getPost('capaian_kpi'),
        ]);

        return redirect()->back()->with('success', 'Catatan evaluasi Monev berhasil disimpan.');
    }

    public function deleteLog(int $logId)
    {
        $log = $this->monevModel->find($logId);
        if ($log) {
            $this->monevModel->delete($logId);
            return redirect()->back()->with('success', 'Log evaluasi berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Data log tidak ditemukan.');
    }

    public function exportExcel()
    {
        $filters = [
            'bidang_kode'  => $this->request->getGet('bidang'),
            'status_monev' => $this->request->getGet('status'),
            'search'       => $this->request->getGet('search'),
        ];

        $prokers = $this->prokerModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Laporan_Monev_Kinerja_Proker_DPC_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Bidang</th>
                <th>Nama Program Kerja</th>
                <th>Waktu / Frekuensi</th>
                <th>PIC</th>
                <th>Target KPI</th>
                <th>Realisasi Capaian</th>
                <th>Status Monev</th>
                <th>Capaian Progres (%)</th>
              </tr>";

        $no = 1;
        foreach ($prokers as $p) {
            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . esc($p['bidang_kode']) . "</td>
                    <td>" . esc($p['nama_program']) . "</td>
                    <td>" . esc($p['waktu_frekuensi'] ?: '-') . "</td>
                    <td>" . esc($p['pic_nama'] ?: '-') . "</td>
                    <td>" . esc($p['kpi_target'] ?: '-') . "</td>
                    <td>" . esc($p['realisasi_capaian'] ?: '-') . "</td>
                    <td>" . strtoupper(esc($p['status_monev'])) . "</td>
                    <td>" . $p['persentase_progres'] . "%</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }
}
