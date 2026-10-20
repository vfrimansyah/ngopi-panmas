<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemiluPartaiModel;

class PartaiController extends BaseController
{
    protected $partaiModel;

    public function __construct()
    {
        $this->partaiModel = new PemiluPartaiModel();
    }

    public function index()
    {
        $data = [
            'title'   => 'Daftar Partai Politik',
            'partai'  => $this->partaiModel->orderBy('nama_partai', 'ASC')->findAll(),
            'pager'   => null
        ];

        $perms = $this->getPagePermissions('pemilu');
        $data = array_merge($data, [
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);

        return view('admin/pemilu_partai/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Partai Politik'
        ];

        return view('admin/pemilu_partai/form', $data);
    }

    public function store()
    {
        $rules = $this->partaiModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $logoName = null;
        $logoFile = $this->request->getFile('lambang_partai');
        
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $logoName = $logoFile->getRandomName();
            $logoFile->move('uploads/partai/', $logoName);
        }

        $this->partaiModel->save([
            'nama_partai'    => $this->request->getPost('nama_partai'),
            'lambang_partai' => $logoName,
            'tahun_berdiri'  => $this->request->getPost('tahun_berdiri') ?: null,
            'azas_ideologi'  => $this->request->getPost('azas_ideologi'),
            'alamat'         => $this->request->getPost('alamat'),
            'cakupan'        => $this->request->getPost('cakupan')
        ]);

        return redirect()->to('admin/pemilu/partai')->with('success', 'Partai politik berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $partai = $this->partaiModel->find($id);

        if (!$partai) {
            return redirect()->to('admin/pemilu/partai')->with('error', 'Partai tidak ditemukan.');
        }

        $data = [
            'title'  => 'Edit Partai Politik',
            'partai' => $partai
        ];

        return view('admin/pemilu_partai/form', $data);
    }

    public function update($id)
    {
        $partai = $this->partaiModel->find($id);

        if (!$partai) {
            return redirect()->to('admin/pemilu/partai')->with('error', 'Partai tidak ditemukan.');
        }

        $rules = $this->partaiModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $logoName = $partai['lambang_partai'];
        $logoFile = $this->request->getFile('lambang_partai');

        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Delete old file if exists
            if (!empty($partai['lambang_partai']) && file_exists('uploads/partai/' . $partai['lambang_partai'])) {
                unlink('uploads/partai/' . $partai['lambang_partai']);
            }
            $logoName = $logoFile->getRandomName();
            $logoFile->move('uploads/partai/', $logoName);
        }

        $this->partaiModel->update($id, [
            'nama_partai'    => $this->request->getPost('nama_partai'),
            'lambang_partai' => $logoName,
            'tahun_berdiri'  => $this->request->getPost('tahun_berdiri') ?: null,
            'azas_ideologi'  => $this->request->getPost('azas_ideologi'),
            'alamat'         => $this->request->getPost('alamat'),
            'cakupan'        => $this->request->getPost('cakupan')
        ]);

        return redirect()->to('admin/pemilu/partai')->with('success', 'Partai politik berhasil diperbarui.');
    }

    public function delete($id)
    {
        $partai = $this->partaiModel->find($id);

        if ($partai) {
            if (!empty($partai['lambang_partai']) && file_exists('uploads/partai/' . $partai['lambang_partai'])) {
                unlink('uploads/partai/' . $partai['lambang_partai']);
            }
            $this->partaiModel->delete($id);
            return redirect()->to('admin/pemilu/partai')->with('success', 'Partai politik berhasil dihapus.');
        }

        return redirect()->to('admin/pemilu/partai')->with('error', 'Partai tidak ditemukan.');
    }
}
