<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ArticleModel;
use App\Models\ArticleCategoryModel;
use App\Models\MemberModel;

class NewsController extends BaseController
{
    protected $newsModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->newsModel = new ArticleModel();
        $this->categoryModel = new ArticleCategoryModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Berita - Admin',
            'news' => $this->newsModel->orderBy('created_at', 'DESC')->findAll(),
            'pager' => null,
        ];

        $perms = $this->getPagePermissions('news');
        $data['can_create'] = $perms['canCreate'];
        $data['can_update'] = $perms['canUpdate'];
        $data['can_delete'] = $perms['canDelete'];

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];
        $data['is_full_admin']   = in_array('admin', $userGroups) || in_array('bph', $userGroups);
        $data['current_user_id'] = $user ? $user->id : 0;

        return view('admin/news/index', $data);
    }

    public function create()
    {
        return view('admin/news/form', [
            'title' => 'Tambah Berita - Admin',
            'news' => null,
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'judul'  => 'required|min_length[5]|max_length[255]',
            'konten' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $gambarName = null;

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $gambarName = $gambar->getRandomName();
            $gambar->move('uploads/news', $gambarName);
        }

        $slug = url_title($this->request->getPost('judul'), '-', true);
        $existing = $this->newsModel->where('slug', $slug)->first();
        if ($existing) {
            $slug .= '-' . time();
        }

        $status = $this->request->getPost('status') ?? 'draft';

        // Find current admin's member record ID
        $memberModel = new MemberModel();
        $currentMember = $memberModel->where('user_id', auth()->id())->first();
        $authorId = $currentMember ? $currentMember['id'] : 1;

        $this->newsModel->save([
            'kategori_id'      => $this->request->getPost('category_id') ?: null,
            'judul'            => $this->request->getPost('judul'),
            'slug'             => $slug,
            'tipe'             => 'berita',
            'konten'           => $this->request->getPost('konten'),
            'foto_cover'       => $gambarName,
            'author_member_id' => $authorId,
            'created_by'       => auth()->id(),
            'status'           => $status,
            'views_count'      => 0,
            'published_at'     => $status === 'published' ? date('Y-m-d H:i:s') : null,
        ]);

        return redirect()->to('/admin/news')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $news = $this->newsModel->find($id);
        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->canModifyResource($news['created_by'] ?? null, 'update', 'news')) {
            return redirect()->to('/admin/news')->with('error', 'Anda hanya dapat mengubah berita yang Anda buat sendiri.');
        }

        return view('admin/news/form', [
            'title' => 'Edit Berita - Admin',
            'news' => $news,
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function update($id = null)
    {
        $news = $this->newsModel->find($id);
        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->canModifyResource($news['created_by'] ?? null, 'update', 'news')) {
            return redirect()->to('/admin/news')->with('error', 'Anda hanya dapat mengubah berita yang Anda buat sendiri.');
        }

        $rules = [
            'judul'  => 'required|min_length[5]|max_length[255]',
            'konten' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $gambarName = $news['foto_cover'];

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            if ($gambarName && file_exists('uploads/news/' . $gambarName)) {
                unlink('uploads/news/' . $gambarName);
            }
            $gambarName = $gambar->getRandomName();
            $gambar->move('uploads/news', $gambarName);
        }

        $status = $this->request->getPost('status');
        $publishedAt = $news['published_at'];
        if ($status === 'published' && !$publishedAt) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $this->newsModel->update($id, [
            'kategori_id'  => $this->request->getPost('category_id') ?: null,
            'judul'        => $this->request->getPost('judul'),
            'konten'       => $this->request->getPost('konten'),
            'foto_cover'   => $gambarName,
            'status'       => $status,
            'published_at' => $publishedAt,
        ]);

        return redirect()->to('/admin/news')->with('success', 'Berita berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $news = $this->newsModel->find($id);
        if (!$news) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->canModifyResource($news['created_by'] ?? null, 'delete', 'news')) {
            return redirect()->to('/admin/news')->with('error', 'Anda hanya dapat menghapus berita yang Anda buat sendiri.');
        }

        if ($news['foto_cover'] && file_exists('uploads/news/' . $news['foto_cover'])) {
            unlink('uploads/news/' . $news['foto_cover']);
        }

        $this->newsModel->delete($id);

        return redirect()->to('/admin/news')->with('success', 'Berita berhasil dihapus.');
    }

    // Category management
    public function categories()
    {
        return view('admin/news/categories', [
            'title' => 'Kategori Berita - Admin',
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    public function storeCategory()
    {
        $rules = ['nama_kategori' => 'required|min_length[2]|max_length[100]'];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->categoryModel->save([
            'nama_kategori' => $this->request->getPost('nama_kategori'),
            'tipe' => 'berita',
        ]);

        return redirect()->to('/admin/news/categories')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function deleteCategory($id = null)
    {
        $this->categoryModel->delete($id);
        return redirect()->to('/admin/news/categories')->with('success', 'Kategori berhasil dihapus.');
    }
}
