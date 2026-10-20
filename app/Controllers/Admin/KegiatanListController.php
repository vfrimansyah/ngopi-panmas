<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KegiatanListModel;
use App\Models\KegiatanJenisModel;

class KegiatanListController extends BaseController
{
    protected $kegiatanModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->kegiatanModel = new KegiatanListModel();
        $this->jenisModel    = new KegiatanJenisModel();
    }

    public function index()
    {
        $filters = [
            'from_date'         => $this->request->getGet('from_date'),
            'to_date'           => $this->request->getGet('to_date'),
            'penyelenggara'     => $this->request->getGet('penyelenggara'),
            'jenis_kegiatan_id' => $this->request->getGet('jenis_kegiatan_id'),
            'dpra'              => $this->request->getGet('dpra'),
            'rw'                => $this->request->getGet('rw'),
            'status_kondisi'    => $this->request->getGet('status_kondisi'),
            'search'            => $this->request->getGet('search'),
        ];

        $kegiatans = $this->kegiatanModel->getList($filters);
        $perms     = $this->getPagePermissions('kegiatan');

        $dpraModel = new \App\Models\MasterDpraModel();

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];

        $data = [
            'title'           => 'List Kegiatan',
            'kegiatans'       => $kegiatans,
            'jenisList'       => $this->jenisModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'dpraList'        => $dpraModel->getList(['status' => 'aktif']),
            'filters'         => $filters,
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
            'is_full_admin'   => in_array('admin', $userGroups) || in_array('bph', $userGroups),
            'current_user_id' => $user ? $user->id : 0,
        ];

        return view('admin/kegiatan/list', $data);
    }

    public function create()
    {
        $dpraModel = new \App\Models\MasterDpraModel();

        $data = [
            'title'     => 'Tambah Kegiatan',
            'kegiatan'  => null,
            'jenisList' => $this->jenisModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'dpraList'  => $dpraModel->getList(['status' => 'aktif']),
        ];

        return view('admin/kegiatan/form', $data);
    }

    public function store()
    {
        $rules = [
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Flyer upload
        $flyer     = $this->request->getFile('flyer');
        $flyerName = null;
        if ($flyer && $flyer->isValid() && !$flyer->hasMoved()) {
            $flyerName = $flyer->getRandomName();
            $flyer->move('uploads/kegiatan/', $flyerName);
        }

        $this->kegiatanModel->save([
            'nama_kegiatan'     => $this->request->getPost('nama_kegiatan'),
            'jenis_kegiatan_id' => $this->request->getPost('jenis_kegiatan_id') ?: null,
            'tanggal'           => $this->request->getPost('tanggal') ?: null,
            'jam_mulai'         => $this->request->getPost('jam_mulai') ?: null,
            'jam_selesai'       => $this->request->getPost('jam_selesai') ?: null,
            'penyelenggara'     => $this->request->getPost('penyelenggara'),
            'pic'               => $this->request->getPost('pic'),
            'jumlah_peserta'    => (int) $this->request->getPost('jumlah_peserta'),
            'lokasi_kegiatan'   => $this->request->getPost('lokasi_kegiatan'),
            'dpra'              => $this->request->getPost('dpra'),
            'rw'                => $this->request->getPost('rw'),
            'rt'                => $this->request->getPost('rt'),
            'status_kondisi'    => $this->request->getPost('status_kondisi') ?: 'hijau',
            'tokoh_hadir'       => $this->request->getPost('tokoh_hadir'),
            'tokoh_pks'         => $this->request->getPost('tokoh_pks'),
            'catatan'           => $this->request->getPost('catatan'),
            'link_doc'          => $this->request->getPost('link_doc'),
            'flyer'             => $flyerName,
            'created_by'        => auth()->id(),
        ]);

        return redirect()->to('admin/kegiatan/list')->with('success', 'Data kegiatan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
            return redirect()->to('admin/kegiatan/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($kegiatan['created_by'] ?? null, 'update', 'kegiatan')) {
            return redirect()->to('admin/kegiatan/list')->with('error', 'Anda hanya dapat mengubah kegiatan yang Anda buat sendiri.');
        }

        $dpraModel = new \App\Models\MasterDpraModel();

        $data = [
            'title'     => 'Edit Kegiatan',
            'kegiatan'  => $kegiatan,
            'jenisList' => $this->jenisModel->orderBy('nomor_urut', 'ASC')->findAll(),
            'dpraList'  => $dpraModel->getList(['status' => 'aktif']),
        ];

        return view('admin/kegiatan/form', $data);
    }

    public function update(int $id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
            return redirect()->to('admin/kegiatan/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($kegiatan['created_by'] ?? null, 'update', 'kegiatan')) {
            return redirect()->to('admin/kegiatan/list')->with('error', 'Anda hanya dapat mengubah kegiatan yang Anda buat sendiri.');
        }

        $rules = [
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $flyer     = $this->request->getFile('flyer');
        $flyerName = $kegiatan['flyer'];
        if ($flyer && $flyer->isValid() && !$flyer->hasMoved()) {
            $flyerName = $flyer->getRandomName();
            $flyer->move('uploads/kegiatan/', $flyerName);
        }

        $this->kegiatanModel->update($id, [
            'nama_kegiatan'     => $this->request->getPost('nama_kegiatan'),
            'jenis_kegiatan_id' => $this->request->getPost('jenis_kegiatan_id') ?: null,
            'tanggal'           => $this->request->getPost('tanggal') ?: null,
            'jam_mulai'         => $this->request->getPost('jam_mulai') ?: null,
            'jam_selesai'       => $this->request->getPost('jam_selesai') ?: null,
            'penyelenggara'     => $this->request->getPost('penyelenggara'),
            'pic'               => $this->request->getPost('pic'),
            'jumlah_peserta'    => (int) $this->request->getPost('jumlah_peserta'),
            'lokasi_kegiatan'   => $this->request->getPost('lokasi_kegiatan'),
            'dpra'              => $this->request->getPost('dpra'),
            'rw'                => $this->request->getPost('rw'),
            'rt'                => $this->request->getPost('rt'),
            'status_kondisi'    => $this->request->getPost('status_kondisi') ?: 'hijau',
            'tokoh_hadir'       => $this->request->getPost('tokoh_hadir'),
            'tokoh_pks'         => $this->request->getPost('tokoh_pks'),
            'catatan'           => $this->request->getPost('catatan'),
            'link_doc'          => $this->request->getPost('link_doc'),
            'flyer'             => $flyerName,
        ]);

        return redirect()->to('admin/kegiatan/list')->with('success', 'Data kegiatan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $kegiatan = $this->kegiatanModel->find($id);
        if (!$kegiatan) {
            return redirect()->to('admin/kegiatan/list')->with('error', 'Data tidak ditemukan.');
        }

        if (!$this->canModifyResource($kegiatan['created_by'] ?? null, 'delete', 'kegiatan')) {
            return redirect()->to('admin/kegiatan/list')->with('error', 'Anda hanya dapat menghapus kegiatan yang Anda buat sendiri.');
        }

        $this->kegiatanModel->delete($id);
        return redirect()->to('admin/kegiatan/list')->with('success', 'Data kegiatan berhasil dihapus.');
    }

    /**
     * Export Excel/CSV download
     */
    public function exportExcel()
    {
        $filters = [
            'from_date'         => $this->request->getGet('from_date'),
            'to_date'           => $this->request->getGet('to_date'),
            'penyelenggara'     => $this->request->getGet('penyelenggara'),
            'jenis_kegiatan_id' => $this->request->getGet('jenis_kegiatan_id'),
            'dpra'              => $this->request->getGet('dpra'),
            'rw'                => $this->request->getGet('rw'),
        ];

        $data = $this->kegiatanModel->getList($filters);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="List_Kegiatan_' . date('Ymd_His') . '.xls"');
        header('Cache-Control: max-age=0');

        echo "<table border='1'>";
        echo "<tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th>Nama Kegiatan</th>
                <th>Jenis Kegiatan</th>
                <th>Penyelenggara</th>
                <th>PIC</th>
                <th>Jumlah Peserta</th>
                <th>Lokasi</th>
                <th>DPRa</th>
                <th>RW</th>
                <th>RT</th>
                <th>Tokoh Hadir</th>
                <th>Tokoh PKS</th>
                <th>Catatan</th>
              </tr>";

        $no = 1;
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . esc($row['tanggal']) . "</td>";
            echo "<td>" . esc($row['jam_mulai']) . " - " . esc($row['jam_selesai']) . "</td>";
            echo "<td>" . esc($row['nama_kegiatan']) . "</td>";
            echo "<td>" . esc($row['jenis_kegiatan_nama']) . "</td>";
            echo "<td>" . esc($row['penyelenggara']) . "</td>";
            echo "<td>" . esc($row['pic']) . "</td>";
            echo "<td>" . esc($row['jumlah_peserta']) . "</td>";
            echo "<td>" . esc($row['lokasi_kegiatan']) . "</td>";
            echo "<td>" . esc($row['dpra']) . "</td>";
            echo "<td>" . esc($row['rw']) . "</td>";
            echo "<td>" . esc($row['rt']) . "</td>";
            echo "<td>" . esc($row['tokoh_hadir']) . "</td>";
            echo "<td>" . esc($row['tokoh_pks']) . "</td>";
            echo "<td>" . esc($row['catatan']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
}
