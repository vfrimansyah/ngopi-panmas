<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingRuanganModel;
use App\Models\BookingMasterModel;

class BookingListController extends BaseController
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
        $filters = [
            'from_date'  => $this->request->getGet('from_date'),
            'to_date'    => $this->request->getGet('to_date'),
            'ruangan_id' => $this->request->getGet('ruangan_id'),
            'search'     => $this->request->getGet('search'),
        ];

        $bookings = $this->bookingModel->getList($filters);
        $perms    = $this->getPagePermissions('booking');

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];

        $data = [
            'title'           => 'List Booking Ruangan',
            'bookings'        => $bookings,
            'ruanganList'     => $this->masterModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'filters'         => $filters,
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
            'is_full_admin'   => in_array('admin', $userGroups) || in_array('bph', $userGroups),
            'current_user_id' => $user ? $user->id : 0,
        ];

        return view('admin/booking/list', $data);
    }

    public function edit(int $id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->to('admin/booking/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($booking['created_by'] ?? null, 'update', 'booking')) {
            return redirect()->to('admin/booking/list')->with('error', 'Anda hanya dapat mengubah booking ruangan yang Anda buat sendiri.');
        }

        $perms = $this->getPagePermissions('booking');

        $data = [
            'title'       => 'Edit Booking Ruangan',
            'booking'     => $booking,
            'ruanganList' => $this->masterModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'can_update'  => $perms['canUpdate'],
        ];

        return view('admin/booking/form', $data);
    }

    public function update(int $id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->to('admin/booking/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($booking['created_by'] ?? null, 'update', 'booking')) {
            return redirect()->to('admin/booking/list')->with('error', 'Anda hanya dapat mengubah booking ruangan yang Anda buat sendiri.');
        }

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

        $this->bookingModel->update($id, [
            'tgl_kegiatan'  => $this->request->getPost('tgl_kegiatan'),
            'jam_mulai'     => $this->request->getPost('jam_mulai'),
            'jam_selesai'   => $this->request->getPost('jam_selesai'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'penyelenggara' => $this->request->getPost('penyelenggara'),
            'ruangan_id'    => (int) $this->request->getPost('ruangan_id'),
            'pj_kegiatan'   => $this->request->getPost('pj_kegiatan'),
            'catatan'       => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('admin/booking/list')->with('success', 'Booking ruangan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            return redirect()->to('admin/booking/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($booking['created_by'] ?? null, 'delete', 'booking')) {
            return redirect()->to('admin/booking/list')->with('error', 'Anda hanya dapat menghapus booking ruangan yang Anda buat sendiri.');
        }

        $this->bookingModel->delete($id);
        return redirect()->to('admin/booking/list')->with('success', 'Booking ruangan berhasil dihapus.');
    }

    /**
     * Export Excel List Booking Ruangan
     */
    public function exportExcel()
    {
        $filters = [
            'from_date'  => $this->request->getGet('from_date'),
            'to_date'    => $this->request->getGet('to_date'),
            'ruangan_id' => $this->request->getGet('ruangan_id'),
        ];

        $data = $this->bookingModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="List_Booking_Ruangan_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Nama Kegiatan</th>
                <th>Penyelenggara</th>
                <th>PJ Kegiatan</th>
                <th>Ruangan</th>
                <th>Catatan</th>
              </tr>";

        $no = 1;
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . esc($row['tgl_kegiatan']) . "</td>";
            echo "<td>" . esc($row['jam_mulai']) . " - " . esc($row['jam_selesai']) . "</td>";
            echo "<td>" . esc($row['nama_kegiatan']) . "</td>";
            echo "<td>" . esc($row['penyelenggara']) . "</td>";
            echo "<td>" . esc($row['pj_kegiatan']) . "</td>";
            echo "<td>" . esc($row['nama_ruangan']) . "</td>";
            echo "<td>" . esc($row['catatan']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}
