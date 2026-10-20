<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventModel;

class EventController extends BaseController
{
    protected $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Event - Admin',
            'events' => $this->eventModel->orderBy('created_at', 'DESC')->findAll(),
            'pager' => null,
        ];

        $perms = $this->getPagePermissions('events');
        $data['can_create'] = $perms['canCreate'];
        $data['can_update'] = $perms['canUpdate'];
        $data['can_delete'] = $perms['canDelete'];

        return view('admin/events/index', $data);
    }

    public function create()
    {
        return view('admin/events/form', [
            'title' => 'Tambah Event - Admin',
            'event' => null,
        ]);
    }

    public function store()
    {
        $rules = [
            'judul'         => 'required|min_length[5]|max_length[255]',
            'deskripsi'     => 'required',
            'tanggal_mulai' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $gambarName = null;

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            $gambarName = $gambar->getRandomName();
            $gambar->move('uploads/events', $gambarName);
        }

        $slug = url_title($this->request->getPost('judul'), '-', true);
        $existing = $this->eventModel->where('slug', $slug)->first();
        if ($existing) {
            $slug .= '-' . time();
        }

        $this->eventModel->save([
            'judul'           => $this->request->getPost('judul'),
            'slug'            => $slug,
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'gambar'          => $gambarName,
            'lokasi'          => $this->request->getPost('lokasi'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status'          => $this->request->getPost('status') ?? 'upcoming',
        ]);

        return redirect()->to('/admin/events')->with('success', 'Event berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $event = $this->eventModel->find($id);
        if (!$event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/events/form', [
            'title' => 'Edit Event - Admin',
            'event' => $event,
        ]);
    }

    public function update($id = null)
    {
        $event = $this->eventModel->find($id);
        if (!$event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'judul'         => 'required|min_length[5]|max_length[255]',
            'deskripsi'     => 'required',
            'tanggal_mulai' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $gambar = $this->request->getFile('gambar');
        $gambarName = $event['gambar'];

        if ($gambar && $gambar->isValid() && !$gambar->hasMoved()) {
            if ($gambarName && file_exists('uploads/events/' . $gambarName)) {
                unlink('uploads/events/' . $gambarName);
            }
            $gambarName = $gambar->getRandomName();
            $gambar->move('uploads/events', $gambarName);
        }

        $this->eventModel->update($id, [
            'judul'           => $this->request->getPost('judul'),
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'gambar'          => $gambarName,
            'lokasi'          => $this->request->getPost('lokasi'),
            'tanggal_mulai'   => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'status'          => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/events')->with('success', 'Event berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $event = $this->eventModel->find($id);
        if (!$event) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($event['gambar'] && file_exists('uploads/events/' . $event['gambar'])) {
            unlink('uploads/events/' . $event['gambar']);
        }

        $this->eventModel->delete($id);

        return redirect()->to('/admin/events')->with('success', 'Event berhasil dihapus.');
    }
}
