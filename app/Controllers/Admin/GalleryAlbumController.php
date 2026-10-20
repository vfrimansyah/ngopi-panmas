<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\GalleryAlbumModel;
use App\Models\GalleryItemModel;

class GalleryAlbumController extends BaseController
{
    protected GalleryAlbumModel $albumModel;
    protected GalleryItemModel $itemModel;

    public function __construct()
    {
        $this->albumModel = new GalleryAlbumModel();
        $this->itemModel  = new GalleryItemModel();
    }

    public function index()
    {
        $albums = $this->albumModel->getAlbumsWithStats();
        $perms  = $this->getPagePermissions('gallery');

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];

        $data = [
            'title'           => 'Manajemen Album Kegiatan Galeri',
            'albums'          => $albums,
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
            'is_full_admin'   => in_array('admin', $userGroups) || in_array('bph', $userGroups),
            'current_user_id' => $user ? $user->id : 0,
        ];

        return view('admin/gallery/albums', $data);
    }

    public function store()
    {
        $rules = [
            'nama_album' => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = trim((string)$this->request->getPost('nama_album'));
        $slug = url_title($nama, '-', true);

        $exists = $this->albumModel->where('slug', $slug)->first();
        if ($exists) {
            $slug .= '-' . time();
        }

        $coverName = null;
        $coverFile = $this->request->getFile('cover_image');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/gallery';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $coverName = 'album_' . time() . '_' . $coverFile->getRandomName();
            $coverFile->move($uploadDir, $coverName);
        }

        $this->albumModel->insert([
            'nama_album'       => $nama,
            'slug'             => $slug,
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'cover_image'      => $coverName ? ('uploads/gallery/' . $coverName) : null,
            'tanggal_kegiatan' => $this->request->getPost('tanggal_kegiatan') ?: null,
            'lokasi'           => $this->request->getPost('lokasi'),
            'sort_order'       => (int)$this->request->getPost('sort_order') ?: 1,
            'created_by'       => auth()->id(),
        ]);

        return redirect()->to(base_url('admin/gallery/albums'))->with('success', 'Album kegiatan berhasil dibuat.');
    }

    public function update(int $id)
    {
        $album = $this->albumModel->find($id);
        if (!$album) {
            return redirect()->to(base_url('admin/gallery/albums'))->with('errors', ['Album tidak ditemukan.']);
        }

        if (!$this->canModifyResource($album['created_by'] ?? null, 'update', 'gallery')) {
            return redirect()->to(base_url('admin/gallery/albums'))->with('errors', ['Anda hanya dapat mengubah album yang Anda buat sendiri.']);
        }

        $rules = [
            'nama_album' => 'required|min_length[3]|max_length[150]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nama_album'       => trim((string)$this->request->getPost('nama_album')),
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'tanggal_kegiatan' => $this->request->getPost('tanggal_kegiatan') ?: null,
            'lokasi'           => $this->request->getPost('lokasi'),
            'sort_order'       => (int)$this->request->getPost('sort_order') ?: 1,
        ];

        $coverFile = $this->request->getFile('cover_image');
        if ($coverFile && $coverFile->isValid() && !$coverFile->hasMoved()) {
            $uploadDir = FCPATH . 'uploads/gallery';
            $coverName = 'album_' . time() . '_' . $coverFile->getRandomName();
            $coverFile->move($uploadDir, $coverName);

            // Delete old cover
            if (!empty($album['cover_image'])) {
                $oldPath = FCPATH . $album['cover_image'];
                if (file_exists($oldPath) && is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $updateData['cover_image'] = 'uploads/gallery/' . $coverName;
        }

        $this->albumModel->update($id, $updateData);

        return redirect()->to(base_url('admin/gallery/albums'))->with('success', 'Album berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $album = $this->albumModel->find($id);
        if (!$album) {
            return redirect()->to(base_url('admin/gallery/albums'))->with('errors', ['Album tidak ditemukan.']);
        }

        if (!$this->canModifyResource($album['created_by'] ?? null, 'delete', 'gallery')) {
            return redirect()->to(base_url('admin/gallery/albums'))->with('errors', ['Anda hanya dapat menghapus album yang Anda buat sendiri.']);
        }

        // Set items album_id to null
        $this->itemModel->where('album_id', $id)->set(['album_id' => null])->update();

        // Delete cover file
        if (!empty($album['cover_image'])) {
            $oldPath = FCPATH . $album['cover_image'];
            if (file_exists($oldPath) && is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        $this->albumModel->delete($id);

        return redirect()->to(base_url('admin/gallery/albums'))->with('success', 'Album berhasil dihapus.');
    }
}
