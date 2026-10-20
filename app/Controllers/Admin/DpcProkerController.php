<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DpcBidangModel;
use App\Models\DpcProkerModel;

class DpcProkerController extends BaseController
{
    protected $prokerModel;
    protected $bidangModel;

    public function __construct()
    {
        $this->prokerModel = new DpcProkerModel();
        $this->bidangModel = new DpcBidangModel();
    }

    public function index()
    {
        $viewMode   = $this->request->getGet('mode') ?: 'matriks'; // 'matriks' (per bidang) or 'list' or 'prioritas'
        $bidangKode = $this->request->getGet('bidang');
        $status     = $this->request->getGet('status');
        $search     = $this->request->getGet('search');

        $filters = [
            'bidang_kode'  => $bidangKode,
            'status_monev' => $status,
            'search'       => $search,
        ];
        if ($viewMode === 'prioritas') {
            $filters['is_prioritas'] = 1;
        }

        $prokers = $this->prokerModel->getList($filters);
        $bidangs = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();

        // Group prokers by bidang for matriks view
        $prokerByBidang = [];
        foreach ($prokers as $p) {
            $prokerByBidang[$p['bidang_kode']][] = $p;
        }

        $perms = $this->getPagePermissions('dpc');

        $data = [
            'title'          => 'Matriks & Program Kerja Bidang DPC',
            'prokers'        => $prokers,
            'prokerByBidang' => $prokerByBidang,
            'bidangs'        => $bidangs,
            'viewMode'       => $viewMode,
            'filters'        => $filters,
            'can_create'     => $perms['canCreate'],
            'can_update'     => $perms['canUpdate'],
            'can_delete'     => $perms['canDelete'],
        ];

        return view('admin/dpc/proker/index', $data);
    }

    public function create()
    {
        $bidangs = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();
        $db = \Config\Database::connect();
        $members = $db->table('members')->select('nama_lengkap')->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        $selectedBidang = $this->request->getGet('bidang');

        $data = [
            'title'          => 'Tambah Program Kerja Baru',
            'proker'         => null,
            'selectedBidang' => $selectedBidang,
            'bidangs'        => $bidangs,
            'memberList'     => array_column($members, 'nama_lengkap'),
        ];

        return view('admin/dpc/proker/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_program' => 'required|min_length[3]|max_length[255]',
            'bidang_kode'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prokerModel->save([
            'bidang_kode'        => $this->request->getPost('bidang_kode'),
            'nama_program'       => $this->request->getPost('nama_program'),
            'deskripsi'          => $this->request->getPost('deskripsi'),
            'waktu_frekuensi'    => $this->request->getPost('waktu_frekuensi'),
            'kpi_target'         => $this->request->getPost('kpi_target'),
            'pic_nama'           => $this->request->getPost('pic_nama'),
            'target_capaian'     => $this->request->getPost('target_capaian'),
            'realisasi_capaian'  => $this->request->getPost('realisasi_capaian'),
            'status_monev'       => $this->request->getPost('status_monev') ?: 'belum',
            'persentase_progres' => (int)$this->request->getPost('persentase_progres') ?: 0,
            'is_prioritas'       => $this->request->getPost('is_prioritas') ? 1 : 0,
        ]);

        return redirect()->to('admin/dpc/proker')->with('success', 'Program kerja berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $proker = $this->prokerModel->find($id);
        if (!$proker) {
            return redirect()->to('admin/dpc/proker')->with('error', 'Program kerja tidak ditemukan.');
        }

        $bidangs = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();
        $db = \Config\Database::connect();
        $members = $db->table('members')->select('nama_lengkap')->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        $data = [
            'title'      => 'Edit Program Kerja',
            'proker'     => $proker,
            'bidangs'    => $bidangs,
            'memberList' => array_column($members, 'nama_lengkap'),
        ];

        return view('admin/dpc/proker/form', $data);
    }

    public function update(int $id)
    {
        $proker = $this->prokerModel->find($id);
        if (!$proker) {
            return redirect()->to('admin/dpc/proker')->with('error', 'Program kerja tidak ditemukan.');
        }

        $rules = [
            'nama_program' => 'required|min_length[3]|max_length[255]',
            'bidang_kode'  => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prokerModel->update($id, [
            'bidang_kode'        => $this->request->getPost('bidang_kode'),
            'nama_program'       => $this->request->getPost('nama_program'),
            'deskripsi'          => $this->request->getPost('deskripsi'),
            'waktu_frekuensi'    => $this->request->getPost('waktu_frekuensi'),
            'kpi_target'         => $this->request->getPost('kpi_target'),
            'pic_nama'           => $this->request->getPost('pic_nama'),
            'target_capaian'     => $this->request->getPost('target_capaian'),
            'realisasi_capaian'  => $this->request->getPost('realisasi_capaian'),
            'status_monev'       => $this->request->getPost('status_monev') ?: 'belum',
            'persentase_progres' => (int)$this->request->getPost('persentase_progres') ?: 0,
            'is_prioritas'       => $this->request->getPost('is_prioritas') ? 1 : 0,
        ]);

        return redirect()->to('admin/dpc/proker')->with('success', 'Program kerja berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $proker = $this->prokerModel->find($id);
        if ($proker) {
            $this->prokerModel->delete($id);
            return redirect()->to('admin/dpc/proker')->with('success', 'Program kerja berhasil dihapus.');
        }
        return redirect()->to('admin/dpc/proker')->with('error', 'Data tidak ditemukan.');
    }

    public function exportExcel()
    {
        $filters = [
            'bidang_kode'  => $this->request->getGet('bidang'),
            'status_monev' => $this->request->getGet('status'),
            'search'       => $this->request->getGet('search'),
        ];
        if ($this->request->getGet('mode') === 'prioritas') {
            $filters['is_prioritas'] = 1;
        }

        $data = $this->prokerModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Matriks_Program_Kerja_DPC_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Bidang</th>
                <th>Nama Program Kerja</th>
                <th>Waktu / Frekuensi</th>
                <th>PIC</th>
                <th>Target KPI</th>
                <th>Status Monev</th>
                <th>Progres (%)</th>
                <th>Prioritas</th>
                <th>Deskripsi</th>
              </tr>";

        $no = 1;
        foreach ($data as $d) {
            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . esc($d['bidang_kode']) . "</td>
                    <td>" . esc($d['nama_program']) . "</td>
                    <td>" . esc($d['waktu_frekuensi'] ?: '-') . "</td>
                    <td>" . esc($d['pic_nama'] ?: '-') . "</td>
                    <td>" . esc($d['kpi_target'] ?: '-') . "</td>
                    <td>" . strtoupper(esc($d['status_monev'])) . "</td>
                    <td>" . $d['persentase_progres'] . "%</td>
                    <td>" . ($d['is_prioritas'] ? 'Ya' : 'Tidak') . "</td>
                    <td>" . esc($d['deskripsi'] ?: '-') . "</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }
}
