<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingMasterModel;

class BookingMasterController extends BaseController
{
    protected $masterModel;

    public function __construct()
    {
        $this->masterModel = new BookingMasterModel();
    }

    public function index()
    {
        $perms = $this->getPagePermissions('booking');

        $data = [
            'title'      => 'Master Ruangan',
            'ruanganList'=> $this->masterModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ];

        return view('admin/booking/master', $data);
    }

    public function store()
    {
        if (!$this->validate(['nama_ruangan' => 'required|min_length[2]|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->masterModel->save([
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'nomor_urut'   => (int) $this->request->getPost('nomor_urut'),
            'kapasitas'    => (int) $this->request->getPost('kapasitas'),
            'keterangan'   => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('admin/booking/master')->with('success', 'Master Ruangan berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $item = $this->masterModel->find($id);
        if (!$item) {
            return redirect()->to('admin/booking/master')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->validate(['nama_ruangan' => 'required|min_length[2]|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->masterModel->update($id, [
            'nama_ruangan' => $this->request->getPost('nama_ruangan'),
            'nomor_urut'   => (int) $this->request->getPost('nomor_urut'),
            'kapasitas'    => (int) $this->request->getPost('kapasitas'),
            'keterangan'   => $this->request->getPost('keterangan'),
        ]);

        return redirect()->to('admin/booking/master')->with('success', 'Master Ruangan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $item = $this->masterModel->find($id);
        if ($item) {
            $this->masterModel->delete($id);
            return redirect()->to('admin/booking/master')->with('success', 'Master Ruangan berhasil dihapus.');
        }
        return redirect()->to('admin/booking/master')->with('error', 'Data tidak ditemukan.');
    }
}
