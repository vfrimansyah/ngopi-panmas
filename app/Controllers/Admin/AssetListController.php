<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AssetModel;

class AssetListController extends BaseController
{
    protected $assetModel;

    public function __construct()
    {
        $this->assetModel = new AssetModel();
    }

    public function index()
    {
        $filters = [
            'lokasi'  => $this->request->getGet('lokasi'),
            'kondisi' => $this->request->getGet('kondisi'),
            'search'  => $this->request->getGet('search'),
        ];

        $perms = $this->getPagePermissions('aset');

        $db = \Config\Database::connect();
        $lokasiOptions = $db->table('assets')->select('lokasi')->distinct()->orderBy('lokasi')->get()->getResultArray();

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];

        $data = [
            'title'           => 'Daftar Aset & Inventaris',
            'assets'          => $this->assetModel->getList($filters),
            'filters'         => $filters,
            'lokasiOptions'   => array_column($lokasiOptions, 'lokasi'),
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
            'is_full_admin'   => in_array('admin', $userGroups) || in_array('bph', $userGroups),
            'current_user_id' => $user ? $user->id : 0,
        ];

        return view('admin/aset/list', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $lokasiOptions = $db->table('assets')->select('lokasi')->distinct()->orderBy('lokasi')->get()->getResultArray();
        $members = $db->table('members')->select('nama_lengkap')->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        $data = [
            'title'         => 'Tambah Aset Baru',
            'asset'         => null,
            'lokasiOptions' => array_column($lokasiOptions, 'lokasi'),
            'memberList'    => array_column($members, 'nama_lengkap'),
        ];

        return view('admin/aset/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_barang' => 'required|min_length[2]|max_length[255]',
            'jumlah'      => 'required|is_natural_no_zero',
            'foto'        => 'permit_empty|is_image[foto]|max_size[foto,5120]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Auto-generate kode_aset
        $lastId = $this->assetModel->orderBy('id', 'DESC')->first();
        $nextNum = $lastId ? ($lastId['id'] + 1) : 1;
        $kodeAset = 'AST-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $fotoName = null;
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            $fotoName = $fotoFile->getRandomName();
            $fotoFile->move(FCPATH . 'uploads/aset', $fotoName);
        }

        $this->assetModel->save([
            'kode_aset'        => $kodeAset,
            'nama_barang'      => $this->request->getPost('nama_barang'),
            'merk'             => $this->request->getPost('merk') ?: null,
            'model'            => $this->request->getPost('model') ?: null,
            'lokasi'           => $this->request->getPost('lokasi'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'created_by'       => auth()->id(),
            'jumlah'           => (int) $this->request->getPost('jumlah'),
            'kondisi'          => $this->request->getPost('kondisi') ?: 'baik',
            'keterangan'       => $this->request->getPost('keterangan'),
            'foto'             => $fotoName,
        ]);

        return redirect()->to('admin/aset/list')->with('success', 'Data aset berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $asset = $this->assetModel->find($id);
        if (!$asset) {
            return redirect()->to('admin/aset/list')->with('error', 'Data aset tidak ditemukan.');
        }

        if (!$this->canModifyResource($asset['created_by'] ?? null, 'update', 'aset')) {
            return redirect()->to('admin/aset/list')->with('error', 'Anda hanya dapat mengubah data aset yang Anda buat sendiri.');
        }

        $db = \Config\Database::connect();
        $lokasiOptions = $db->table('assets')->select('lokasi')->distinct()->orderBy('lokasi')->get()->getResultArray();
        $members = $db->table('members')->select('nama_lengkap')->where('is_active', 1)->orderBy('nama_lengkap', 'ASC')->get()->getResultArray();

        $data = [
            'title'         => 'Edit Aset',
            'asset'         => $asset,
            'lokasiOptions' => array_column($lokasiOptions, 'lokasi'),
            'memberList'    => array_column($members, 'nama_lengkap'),
        ];

        return view('admin/aset/form', $data);
    }

    public function update(int $id)
    {
        $asset = $this->assetModel->find($id);
        if (!$asset) {
            return redirect()->to('admin/aset/list')->with('error', 'Data aset tidak ditemukan.');
        }

        if (!$this->canModifyResource($asset['created_by'] ?? null, 'update', 'aset')) {
            return redirect()->to('admin/aset/list')->with('error', 'Anda hanya dapat mengubah data aset yang Anda buat sendiri.');
        }

        $rules = [
            'nama_barang' => 'required|min_length[2]|max_length[255]',
            'jumlah'      => 'required|is_natural_no_zero',
            'foto'        => 'permit_empty|is_image[foto]|max_size[foto,5120]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fotoName = $asset['foto'] ?? null;
        $fotoFile = $this->request->getFile('foto');
        if ($fotoFile && $fotoFile->isValid() && !$fotoFile->hasMoved()) {
            if (!empty($asset['foto']) && file_exists(FCPATH . 'uploads/aset/' . $asset['foto'])) {
                @unlink(FCPATH . 'uploads/aset/' . $asset['foto']);
            }
            $fotoName = $fotoFile->getRandomName();
            $fotoFile->move(FCPATH . 'uploads/aset', $fotoName);
        } elseif ($this->request->getPost('hapus_foto') === '1') {
            if (!empty($asset['foto']) && file_exists(FCPATH . 'uploads/aset/' . $asset['foto'])) {
                @unlink(FCPATH . 'uploads/aset/' . $asset['foto']);
            }
            $fotoName = null;
        }

        $this->assetModel->update($id, [
            'nama_barang'      => $this->request->getPost('nama_barang'),
            'merk'             => $this->request->getPost('merk') ?: null,
            'model'            => $this->request->getPost('model') ?: null,
            'lokasi'           => $this->request->getPost('lokasi'),
            'penanggung_jawab' => $this->request->getPost('penanggung_jawab'),
            'jumlah'           => (int) $this->request->getPost('jumlah'),
            'kondisi'          => $this->request->getPost('kondisi'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'foto'             => $fotoName,
        ]);

        return redirect()->to('admin/aset/list')->with('success', 'Data aset berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $asset = $this->assetModel->find($id);
        if (!$asset) {
            return redirect()->to('admin/aset/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($asset['created_by'] ?? null, 'delete', 'aset')) {
            return redirect()->to('admin/aset/list')->with('error', 'Anda hanya dapat menghapus data aset yang Anda buat sendiri.');
        }

        if (!empty($asset['foto']) && file_exists(FCPATH . 'uploads/aset/' . $asset['foto'])) {
            @unlink(FCPATH . 'uploads/aset/' . $asset['foto']);
        }

        $this->assetModel->delete($id);
        return redirect()->to('admin/aset/list')->with('success', 'Data aset berhasil dihapus.');
    }

    public function exportExcel()
    {
        $filters = [
            'lokasi'  => $this->request->getGet('lokasi'),
            'kondisi' => $this->request->getGet('kondisi'),
            'search'  => $this->request->getGet('search'),
        ];

        $assets = $this->assetModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="Data_Aset_Inventaris_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        $kondisiLabel = [
            'baik'        => 'Baik',
            'kurang_baik' => 'Kurang Baik',
            'rusak'       => 'Rusak',
            'hilang'      => 'Hilang',
        ];

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Kode Aset</th>
                <th>Nama Barang</th>
                <th>Merk</th>
                <th>Model</th>
                <th>Lokasi</th>
                <th>Penanggung Jawab / Pemegang</th>
                <th>Jumlah</th>
                <th>Kondisi</th>
                <th>Keterangan</th>
              </tr>";

        $no = 1;
        foreach ($assets as $a) {
            echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . esc($a['kode_aset']) . "</td>
                    <td>" . esc($a['nama_barang']) . "</td>
                    <td>" . esc($a['merk'] ?: '-') . "</td>
                    <td>" . esc($a['model'] ?: '-') . "</td>
                    <td>" . esc($a['lokasi']) . "</td>
                    <td>" . esc($a['penanggung_jawab'] ?: '-') . "</td>
                    <td>" . $a['jumlah'] . "</td>
                    <td>" . ($kondisiLabel[$a['kondisi']] ?? ucfirst($a['kondisi'])) . "</td>
                    <td>" . esc($a['keterangan']) . "</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    }
}
