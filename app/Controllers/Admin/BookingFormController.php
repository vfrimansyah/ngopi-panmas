<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingRuanganModel;
use App\Models\BookingMasterModel;

class BookingFormController extends BaseController
{
    protected $bookingModel;
    protected $masterModel;

    public function __construct()
    {
        $this->bookingModel = new BookingRuanganModel();
        $this->masterModel  = new BookingMasterModel();
    }

    public function index()
    {
        $perms = $this->getPagePermissions('booking');

        $data = [
            'title'       => 'Booking Ruangan',
            'ruanganList' => $this->masterModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'can_create'  => $perms['canCreate'],
            'can_update'  => $perms['canUpdate'],
            'can_delete'  => $perms['canDelete'],
        ];

        return view('admin/booking/form', $data);
    }

    public function store()
    {
        $rules = [
            'tgl_kegiatan'  => 'required|valid_date',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'ruangan_id'    => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->bookingModel->save([
            'tgl_kegiatan'  => $this->request->getPost('tgl_kegiatan'),
            'jam_mulai'     => $this->request->getPost('jam_mulai'),
            'jam_selesai'   => $this->request->getPost('jam_selesai'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'penyelenggara' => $this->request->getPost('penyelenggara'),
            'ruangan_id'    => (int) $this->request->getPost('ruangan_id'),
            'pj_kegiatan'   => $this->request->getPost('pj_kegiatan'),
            'catatan'       => $this->request->getPost('catatan'),
            'created_by'    => auth()->id(),
        ]);

        return redirect()->to('admin/booking/list')->with('success', 'Booking ruangan berhasil disimpan.');
    }
}
