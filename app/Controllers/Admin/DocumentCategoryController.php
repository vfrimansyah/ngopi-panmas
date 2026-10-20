<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentCategoryModel;
use App\Models\DocumentModel;

class DocumentCategoryController extends BaseController
{
    protected DocumentCategoryModel $catModel;
    protected DocumentModel $docModel;

    public function __construct()
    {
        $this->catModel = new DocumentCategoryModel();
        $this->docModel = new DocumentModel();
    }



    public function index()
    {
        $categories = $this->catModel->getCategoriesWithStats();
        $perms = $this->getPagePermissions('documents');

        $data = [
            'title'      => 'Kategori Dokumen & Klasifikasi Arsip',
            'categories' => $categories,
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ];

        return view('admin/documents/categories', $data);
    }

    public function store()
    {
        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = $this->request->getPost('nama_kategori');
        $slug = url_title($nama, '-', true);

        // Check unique slug
        $exists = $this->catModel->where('slug', $slug)->first();
        if ($exists) {
            $slug .= '-' . time();
        }

        $this->catModel->insert([
            'nama_kategori' => $nama,
            'slug'          => $slug,
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'icon'          => $this->request->getPost('icon') ?: 'fa-folder',
            'warna'         => $this->request->getPost('warna') ?: '#3b82f6',
            'sort_order'    => (int)$this->request->getPost('sort_order') ?: 1,
        ]);

        return redirect()->to(base_url('admin/documents/categories'))->with('success', 'Kategori dokumen berhasil ditambahkan.');
    }

    public function update(int $id)
    {
        $cat = $this->catModel->find($id);
        if (!$cat) {
            return redirect()->to(base_url('admin/documents/categories'))->with('errors', ['Kategori tidak ditemukan.']);
        }

        $rules = [
            'nama_kategori' => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->catModel->update($id, [
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'icon'          => $this->request->getPost('icon') ?: 'fa-folder',
            'warna'         => $this->request->getPost('warna') ?: '#3b82f6',
            'sort_order'    => (int)$this->request->getPost('sort_order') ?: 1,
        ]);

        return redirect()->to(base_url('admin/documents/categories'))->with('success', 'Kategori dokumen berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $cat = $this->catModel->find($id);
        if (!$cat) {
            return redirect()->to(base_url('admin/documents/categories'))->with('errors', ['Kategori tidak ditemukan.']);
        }

        // Set documents under this category to null
        $this->docModel->where('kategori_id', $id)->set(['kategori_id' => null])->update();
        $this->catModel->delete($id);

        return redirect()->to(base_url('admin/documents/categories'))->with('success', 'Kategori berhasil dihapus.');
    }
}
