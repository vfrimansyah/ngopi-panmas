<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DpcPengurusModel;

class DpcStrukturController extends BaseController
{
    protected $pengurusModel;

    public function __construct()
    {
        $this->pengurusModel = new DpcPengurusModel();
    }

    public function index()
    {
        $activeTab = $this->request->getGet('tab') ?: 'dpc';
        $search    = $this->request->getGet('search');

        $filters = ['search' => $search];
        if ($activeTab === 'dpc') {
            $filters['institusi'] = 'DPC';
        } elseif ($activeTab !== 'all') {
            $filters['institusi'] = $activeTab;
        }

        $allPengurus = $this->pengurusModel->getList($filters);

        // Get unique institutions for tab filtering
        $db = \Config\Database::connect();
        $institusiList = $db->table('dpc_pengurus')->select('institusi, MIN(id) as min_id')->groupBy('institusi')->orderBy('min_id', 'ASC')->get()->getResultArray();
        $institusiOptions = array_column($institusiList, 'institusi');

        // Grouping DPC Pengurus for Org Chart Visual
        $dpcBph = $this->pengurusModel->where('institusi', 'DPC')->where('bidang_kode', 'BPH')->orderBy('sort_order', 'ASC')->findAll();
        $dpcBidang = $this->pengurusModel->where('institusi', 'DPC')->where('bidang_kode !=', 'BPH')->orderBy('sort_order', 'ASC')->findAll();

        $perms = $this->getPagePermissions('dpc');

        $data = [
            'title'            => 'Struktur Pengurus DPC & DPRa',
            'pengurus'         => $allPengurus,
            'activeTab'        => $activeTab,
            'search'           => $search,
            'institusiOptions' => $institusiOptions,
            'dpcBph'           => $dpcBph,
            'dpcBidang'        => $dpcBidang,
            'can_create'       => $perms['canCreate'],
            'can_update'       => $perms['canUpdate'],
            'can_delete'       => $perms['canDelete'],
        ];

        return view('admin/dpc/struktur/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $members = $db->table('members')->select('nama_lengkap')->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();
        $bidangs = $db->table('dpc_bidang')->select('kode, nama_bidang')->orderBy('sort_order', 'ASC')->get()->getResultArray();

        $data = [
            'title'      => 'Tambah Pengurus Baru',
            'pengurus'   => null,
            'memberList' => array_column($members, 'nama_lengkap'),
            'bidangs'    => $bidangs,
        ];

        return view('admin/dpc/struktur/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama'      => 'required|min_length[2]|max_length[150]',
            'institusi' => 'required',
            'jabatan'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pengurusModel->save([
            'nama'        => $this->request->getPost('nama'),
            'institusi'   => $this->request->getPost('institusi'),
            'jabatan'     => $this->request->getPost('jabatan'),
            'bidang_kode' => $this->request->getPost('bidang_kode') ?: null,
            'no_hp'       => $this->request->getPost('no_hp'),
            'sort_order'  => (int)$this->request->getPost('sort_order') ?: 99,
            'periode'     => $this->request->getPost('periode') ?: '2025-2030',
        ]);

        return redirect()->to('admin/dpc/struktur?tab=' . urlencode($this->request->getPost('institusi')))->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $pengurus = $this->pengurusModel->find($id);
        if (!$pengurus) {
            return redirect()->to('admin/dpc/struktur')->with('error', 'Data pengurus tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $members = $db->table('members')->select('nama_lengkap')->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();
        $bidangs = $db->table('dpc_bidang')->select('kode, nama_bidang')->orderBy('sort_order', 'ASC')->get()->getResultArray();

        $data = [
            'title'      => 'Edit Data Pengurus',
            'pengurus'   => $pengurus,
            'memberList' => array_column($members, 'nama_lengkap'),
            'bidangs'    => $bidangs,
        ];

        return view('admin/dpc/struktur/form', $data);
    }

    public function update(int $id)
    {
        $pengurus = $this->pengurusModel->find($id);
        if (!$pengurus) {
            return redirect()->to('admin/dpc/struktur')->with('error', 'Data pengurus tidak ditemukan.');
        }

        $rules = [
            'nama'      => 'required|min_length[2]|max_length[150]',
            'institusi' => 'required',
            'jabatan'   => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pengurusModel->update($id, [
            'nama'        => $this->request->getPost('nama'),
            'institusi'   => $this->request->getPost('institusi'),
            'jabatan'     => $this->request->getPost('jabatan'),
            'bidang_kode' => $this->request->getPost('bidang_kode') ?: null,
            'no_hp'       => $this->request->getPost('no_hp'),
            'sort_order'  => (int)$this->request->getPost('sort_order') ?: 99,
            'periode'     => $this->request->getPost('periode') ?: '2025-2030',
        ]);

        return redirect()->to('admin/dpc/struktur?tab=' . urlencode($this->request->getPost('institusi')))->with('success', 'Data pengurus berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $pengurus = $this->pengurusModel->find($id);
        if ($pengurus) {
            $this->pengurusModel->delete($id);
            return redirect()->to('admin/dpc/struktur')->with('success', 'Data pengurus berhasil dihapus.');
        }
        return redirect()->to('admin/dpc/struktur')->with('error', 'Data tidak ditemukan.');
    }

    public function exportExcel()
    {
        $institusi = $this->request->getGet('institusi');
        $filters = [];
        if (!empty($institusi) && $institusi !== 'all') {
            $filters['institusi'] = $institusi;
        }

        $data = $this->pengurusModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Struktur_Pengurus_DPC_DPRa_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Nama</th>
                <th>Institusi</th>
                <th>Jabatan</th>
                <th>Bidang / Divisi</th>
                <th>No HP / WhatsApp</th>
                <th>Periode</th>
              </tr>";

        $no = 1;
        foreach ($data as $d) {
            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . esc($d['nama']) . "</td>
                    <td>" . esc($d['institusi']) . "</td>
                    <td>" . esc($d['jabatan']) . "</td>
                    <td>" . esc($d['bidang_kode'] ?: '-') . "</td>
                    <td>" . esc($d['no_hp'] ?: '-') . "</td>
                    <td>" . esc($d['periode']) . "</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }
}
