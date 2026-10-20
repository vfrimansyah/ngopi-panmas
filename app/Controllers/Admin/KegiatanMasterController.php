<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KegiatanJenisModel;

class KegiatanMasterController extends BaseController
{
    protected $jenisModel;

    public function __construct()
    {
        $this->jenisModel = new KegiatanJenisModel();
    }

    public function index()
    {
        $perms = $this->getPagePermissions('kegiatan');

        $data = [
            'title'      => 'Master Kegiatan',
            'jenisList'  => $this->jenisModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ];

        return view('admin/kegiatan/master', $data);
    }

    public function store()
    {
        if (!$this->validate(['nama_kegiatan' => 'required|min_length[2]|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jenisModel->save([
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'nomor_urut'    => (int) $this->request->getPost('nomor_urut'),
        ]);

        return redirect()->to('admin/kegiatan/master')->with('success', 'Master Jenis Kegiatan berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $item = $this->jenisModel->find($id);
        if (!$item) {
            return redirect()->to('admin/kegiatan/master')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->validate(['nama_kegiatan' => 'required|min_length[2]|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->jenisModel->update($id, [
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'nomor_urut'    => (int) $this->request->getPost('nomor_urut'),
        ]);

        return redirect()->to('admin/kegiatan/master')->with('success', 'Master Jenis Kegiatan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $item = $this->jenisModel->find($id);
        if ($item) {
            $this->jenisModel->delete($id);
            return redirect()->to('admin/kegiatan/master')->with('success', 'Master Jenis Kegiatan berhasil dihapus.');
        }
        return redirect()->to('admin/kegiatan/master')->with('error', 'Data tidak ditemukan.');
    }
}
