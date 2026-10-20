<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DpcBidangModel;
use App\Models\DpcRabModel;

class DpcRabController extends BaseController
{
    protected $rabModel;
    protected $bidangModel;

    public function __construct()
    {
        $this->rabModel = new DpcRabModel();
        $this->bidangModel = new DpcBidangModel();
    }

    public function index()
    {
        $bidangKode = $this->request->getGet('bidang');
        $search     = $this->request->getGet('search');

        $filters = [
            'bidang_kode' => $bidangKode,
            'search'      => $search,
        ];

        $rabList    = $this->rabModel->getList($filters);
        $bidangs    = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();
        $summary    = $this->rabModel->getSummaryByBidang();

        // Calculate grand totals
        $totalAnggaran = 0;
        $totalRealisasi = 0;
        foreach ($rabList as $r) {
            $totalAnggaran += (float)$r['anggaran'];
            $totalRealisasi += (float)$r['realisasi_anggaran'];
        }

        $perms = $this->getPagePermissions('dpc');

        $data = [
            'title'          => 'Rencana Anggaran Biaya (RAB) Program',
            'rabList'        => $rabList,
            'bidangs'        => $bidangs,
            'summary'        => $summary,
            'totalAnggaran'  => $totalAnggaran,
            'totalRealisasi' => $totalRealisasi,
            'filters'        => $filters,
            'can_create'     => $perms['canCreate'],
            'can_update'     => $perms['canUpdate'],
            'can_delete'     => $perms['canDelete'],
        ];

        return view('admin/dpc/rab/index', $data);
    }

    public function create()
    {
        $bidangs = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'   => 'Tambah Item RAB Program',
            'rab'     => null,
            'bidangs' => $bidangs,
        ];

        return view('admin/dpc/rab/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_program' => 'required|min_length[3]|max_length[255]',
            'bidang_kode'  => 'required',
            'anggaran'     => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->rabModel->save([
            'bidang_kode'        => $this->request->getPost('bidang_kode'),
            'nama_program'       => $this->request->getPost('nama_program'),
            'waktu_frekuensi'    => $this->request->getPost('waktu_frekuensi'),
            'anggaran'           => (float)$this->request->getPost('anggaran'),
            'realisasi_anggaran' => (float)$this->request->getPost('realisasi_anggaran') ?: 0.00,
            'keterangan'         => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('admin/dpc/rab')->with('success', 'Data RAB berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $rab = $this->rabModel->find($id);
        if (!$rab) {
            return redirect()->to('admin/dpc/rab')->with('error', 'Data RAB tidak ditemukan.');
        }

        $bidangs = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'   => 'Edit Item RAB Program',
            'rab'     => $rab,
            'bidangs' => $bidangs,
        ];

        return view('admin/dpc/rab/form', $data);
    }

    public function update(int $id)
    {
        $rab = $this->rabModel->find($id);
        if (!$rab) {
            return redirect()->to('admin/dpc/rab')->with('error', 'Data RAB tidak ditemukan.');
        }

        $rules = [
            'nama_program' => 'required|min_length[3]|max_length[255]',
            'bidang_kode'  => 'required',
            'anggaran'     => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->rabModel->update($id, [
            'bidang_kode'        => $this->request->getPost('bidang_kode'),
            'nama_program'       => $this->request->getPost('nama_program'),
            'waktu_frekuensi'    => $this->request->getPost('waktu_frekuensi'),
            'anggaran'           => (float)$this->request->getPost('anggaran'),
            'realisasi_anggaran' => (float)$this->request->getPost('realisasi_anggaran') ?: 0.00,
            'keterangan'         => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('admin/dpc/rab')->with('success', 'Data RAB berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $rab = $this->rabModel->find($id);
        if ($rab) {
            $this->rabModel->delete($id);
            return redirect()->to('admin/dpc/rab')->with('success', 'Data RAB berhasil dihapus.');
        }
        return redirect()->to('admin/dpc/rab')->with('error', 'Data tidak ditemukan.');
    }

    public function exportExcel()
    {
        $filters = [
            'bidang_kode' => $this->request->getGet('bidang'),
            'search'      => $this->request->getGet('search'),
        ];

        $data = $this->rabModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="RAB_Program_Kerja_DPC_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Bidang</th>
                <th>Nama Program Prioritas</th>
                <th>Waktu / Frekuensi</th>
                <th>Alokasi Anggaran (Rp)</th>
                <th>Realisasi (Rp)</th>
                <th>Sisa Anggaran (Rp)</th>
                <th>Keterangan</th>
              </tr>";

        $no = 1;
        $tAng = 0;
        $tReal = 0;
        foreach ($data as $d) {
            $sisa = (float)$d['anggaran'] - (float)$d['realisasi_anggaran'];
            $tAng += (float)$d['anggaran'];
            $tReal += (float)$d['realisasi_anggaran'];
            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . esc($d['bidang_kode']) . "</td>
                    <td>" . esc($d['nama_program']) . "</td>
                    <td>" . esc($d['waktu_frekuensi'] ?: '-') . "</td>
                    <td>" . number_format((float)$d['anggaran'], 0, ',', '.') . "</td>
                    <td>" . number_format((float)$d['realisasi_anggaran'], 0, ',', '.') . "</td>
                    <td>" . number_format($sisa, 0, ',', '.') . "</td>
                    <td>" . esc($d['keterangan'] ?: '-') . "</td>
                  </tr>";
        }
        echo "<tr>
                <th colspan='4'>TOTAL KESELURUHAN</th>
                <th>" . number_format($tAng, 0, ',', '.') . "</th>
                <th>" . number_format($tReal, 0, ',', '.') . "</th>
                <th>" . number_format($tAng - $tReal, 0, ',', '.') . "</th>
                <th></th>
              </tr>";
        echo "</table>";
        exit;
    }
}
