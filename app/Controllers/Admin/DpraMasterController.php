<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MasterDpraModel;

class DpraMasterController extends BaseController
{
    protected $dpraModel;

    public function __construct()
    {
        $this->dpraModel = new MasterDpraModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // Ambil daftar kelurahan di Pancoran Mas / Depok untuk dropdown
        $villages = $db->table('reg_villages')
            ->where('district_id', '327601')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        $perms = $this->getPagePermissions('kegiatan');

        $data = [
            'title'      => 'Master DPRa PKS',
            'dpraList'   => $this->dpraModel->getList(),
            'villages'   => $villages,
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ];

        return view('admin/dpra/index', $data);
    }

    public function store()
    {
        $rules = [
            'village_id' => 'required',
            'nama_dpra'  => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->dpraModel->save([
            'village_id' => $this->request->getPost('village_id'),
            'nama_dpra'  => $this->request->getPost('nama_dpra'),
            'kode_dpra'  => $this->request->getPost('kode_dpra'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status') ?: 'aktif',
        ]);

        return redirect()->to('admin/dpra')->with('success', 'Master DPRa PKS berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $item = $this->dpraModel->find($id);
        if (!$item) {
            return redirect()->to('admin/dpra')->with('error', 'Data DPRa tidak ditemukan.');
        }

        $rules = [
            'village_id' => 'required',
            'nama_dpra'  => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->dpraModel->update($id, [
            'village_id' => $this->request->getPost('village_id'),
            'nama_dpra'  => $this->request->getPost('nama_dpra'),
            'kode_dpra'  => $this->request->getPost('kode_dpra'),
            'keterangan' => $this->request->getPost('keterangan'),
            'status'     => $this->request->getPost('status'),
        ]);

        return redirect()->to('admin/dpra')->with('success', 'Master DPRa PKS berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $item = $this->dpraModel->find($id);
        if ($item) {
            $this->dpraModel->delete($id);
            return redirect()->to('admin/dpra')->with('success', 'Master DPRa PKS berhasil dihapus.');
        }
        return redirect()->to('admin/dpra')->with('error', 'Data tidak ditemukan.');
    }
}
