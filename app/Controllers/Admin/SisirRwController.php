<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SisirRwModel;
use App\Models\BentukKegiatanModel;

class SisirRwController extends BaseController
{
    protected $sisirModel;
    protected $bentukModel;
    protected $db;

    // Default district ID for Pancoran Mas (Depok)
    const PANCORAN_MAS_DISTRICT_ID = '327601';

    public function __construct()
    {
        $this->db          = \Config\Database::connect();
        $this->sisirModel  = new SisirRwModel();
        $this->bentukModel = new BentukKegiatanModel();
    }

    // ── Helper: Get Pancoran Mas kelurahan list ─────────────────────────────
    private function getKelurahan(): array
    {
        return $this->db->table('reg_villages')
            ->where('district_id', self::PANCORAN_MAS_DISTRICT_ID)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
    }

    // ── Helper: Get All Kecamatan in Depok (regency 3276) ──────────────────
    private function getKecamatan(): array
    {
        return $this->db->table('reg_districts')
            ->where('regency_id', '3276')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $filters = [
            'status'             => $this->request->getGet('status'),
            'village_id'         => $this->request->getGet('village_id'),
            'bentuk_kegiatan_id' => $this->request->getGet('bentuk_kegiatan_id'),
            'rw'                 => $this->request->getGet('rw'),
        ];

        $perms = $this->getPagePermissions('pemilu');

        $data = [
            'title'       => 'Kegiatan Sisir RW',
            'kegiatans'   => $this->sisirModel->getList($filters),
            'bentukList'  => $this->bentukModel->orderBy('urutan', 'ASC')->findAll(),
            'kelurahan'   => $this->getKelurahan(),
            'filters'     => $filters,
            'can_create'  => $perms['canCreate'],
            'can_update'  => $perms['canUpdate'],
            'can_delete'  => $perms['canDelete'],
        ];

        return view('admin/pemilu_sisir_rw/index', $data);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────
    public function create()
    {
        $data = [
            'title'      => 'Tambah Kegiatan Sisir RW',
            'kegiatan'   => null,
            'bentukList' => $this->bentukModel->orderBy('urutan', 'ASC')->findAll(),
            'kelurahan'  => $this->getKelurahan(),
            'kecamatan'  => $this->getKecamatan(),
        ];

        return view('admin/pemilu_sisir_rw/form', $data);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'status'        => 'required|in_list[hijau,kuning,merah]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Resolve kelurahan name from village_id
        $villageId   = $this->request->getPost('village_id');
        $kelurahanName = '';
        if ($villageId) {
            $v = $this->db->table('reg_villages')->where('id', $villageId)->get()->getRowArray();
            $kelurahanName = $v['name'] ?? '';
        }

        // Resolve kecamatan name from district_id
        $districtId    = $this->request->getPost('district_id');
        $kecamatanName = 'Pancoran Mas';
        if ($districtId) {
            $d = $this->db->table('reg_districts')->where('id', $districtId)->get()->getRowArray();
            $kecamatanName = $d['name'] ?? 'Pancoran Mas';
        }

        $this->sisirModel->save([
            'nama_kegiatan'      => $this->request->getPost('nama_kegiatan'),
            'tanggal'            => $this->request->getPost('tanggal') ?: null,
            'alamat'             => $this->request->getPost('alamat'),
            'rt'                 => $this->request->getPost('rt'),
            'rw'                 => $this->request->getPost('rw'),
            'village_id'         => $villageId ?: null,
            'kelurahan'          => $kelurahanName,
            'kecamatan'          => $kecamatanName,
            'bentuk_kegiatan_id' => $this->request->getPost('bentuk_kegiatan_id') ?: null,
            'jumlah_peserta'     => (int) $this->request->getPost('jumlah_peserta'),
            'tokoh_hadir'        => $this->request->getPost('tokoh_hadir'),
            'agenda_lanjutan'    => $this->request->getPost('agenda_lanjutan'),
            'keterangan'         => $this->request->getPost('keterangan'),
            'status'             => $this->request->getPost('status'),
            'created_by'         => auth()->id(),
        ]);

        return redirect()->to('admin/pemilu/sisir-rw')->with('success', 'Kegiatan Sisir RW berhasil ditambahkan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────────────────────
    public function edit(int $id)
    {
        $kegiatan = $this->sisirModel->find($id);

        if (!$kegiatan) {
            return redirect()->to('admin/pemilu/sisir-rw')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title'      => 'Edit Kegiatan Sisir RW',
            'kegiatan'   => $kegiatan,
            'bentukList' => $this->bentukModel->orderBy('urutan', 'ASC')->findAll(),
            'kelurahan'  => $this->getKelurahan(),
            'kecamatan'  => $this->getKecamatan(),
        ];

        return view('admin/pemilu_sisir_rw/form', $data);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────
    public function update(int $id)
    {
        $kegiatan = $this->sisirModel->find($id);
        if (!$kegiatan) {
            return redirect()->to('admin/pemilu/sisir-rw')->with('error', 'Data tidak ditemukan.');
        }

        $rules = [
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'status'        => 'required|in_list[hijau,kuning,merah]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $villageId   = $this->request->getPost('village_id');
        $kelurahanName = '';
        if ($villageId) {
            $v = $this->db->table('reg_villages')->where('id', $villageId)->get()->getRowArray();
            $kelurahanName = $v['name'] ?? '';
        }

        $districtId    = $this->request->getPost('district_id');
        $kecamatanName = 'Pancoran Mas';
        if ($districtId) {
            $d = $this->db->table('reg_districts')->where('id', $districtId)->get()->getRowArray();
            $kecamatanName = $d['name'] ?? 'Pancoran Mas';
        }

        $this->sisirModel->update($id, [
            'nama_kegiatan'      => $this->request->getPost('nama_kegiatan'),
            'tanggal'            => $this->request->getPost('tanggal') ?: null,
            'alamat'             => $this->request->getPost('alamat'),
            'rt'                 => $this->request->getPost('rt'),
            'rw'                 => $this->request->getPost('rw'),
            'village_id'         => $villageId ?: null,
            'kelurahan'          => $kelurahanName,
            'kecamatan'          => $kecamatanName,
            'bentuk_kegiatan_id' => $this->request->getPost('bentuk_kegiatan_id') ?: null,
            'jumlah_peserta'     => (int) $this->request->getPost('jumlah_peserta'),
            'tokoh_hadir'        => $this->request->getPost('tokoh_hadir'),
            'agenda_lanjutan'    => $this->request->getPost('agenda_lanjutan'),
            'keterangan'         => $this->request->getPost('keterangan'),
            'status'             => $this->request->getPost('status'),
        ]);

        return redirect()->to('admin/pemilu/sisir-rw')->with('success', 'Kegiatan Sisir RW berhasil diperbarui.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DETAIL
    // ─────────────────────────────────────────────────────────────────────────
    public function detail(int $id)
    {
        $kegiatan = $this->sisirModel->getDetail($id);
        if (!$kegiatan) {
            return redirect()->to('admin/pemilu/sisir-rw')->with('error', 'Data tidak ditemukan.');
        }
        $perms = $this->getPagePermissions('pemilu');
        return view('admin/pemilu_sisir_rw/detail', [
            'title'      => 'Detail Kegiatan Sisir RW',
            'kegiatan'   => $kegiatan,
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────
    public function delete(int $id)
    {
        $kegiatan = $this->sisirModel->find($id);
        if ($kegiatan) {
            $this->sisirModel->delete($id);
            return redirect()->to('admin/pemilu/sisir-rw')->with('success', 'Data berhasil dihapus.');
        }
        return redirect()->to('admin/pemilu/sisir-rw')->with('error', 'Data tidak ditemukan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX: Get villages by district ID
    // ─────────────────────────────────────────────────────────────────────────
    public function getVillages()
    {
        $districtId = $this->request->getGet('district_id') ?: self::PANCORAN_MAS_DISTRICT_ID;
        $data = $this->db->table('reg_villages')
            ->where('district_id', $districtId)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Manage master data Bentuk Kegiatan
    // ─────────────────────────────────────────────────────────────────────────
    public function bentuk()
    {
        $perms = $this->getPagePermissions('pemilu');
        $data  = [
            'title'      => 'Master Bentuk Kegiatan',
            'bentukList' => $this->bentukModel->orderBy('urutan', 'ASC')->findAll(),
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ];
        return view('admin/pemilu_sisir_rw/bentuk', $data);
    }

    public function bentukStore()
    {
        if (!$this->validate(['nama' => 'required|max_length[100]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->bentukModel->save([
            'nama'   => $this->request->getPost('nama'),
            'urutan' => (int) $this->request->getPost('urutan'),
        ]);
        return redirect()->to('admin/pemilu/sisir-rw/bentuk')->with('success', 'Bentuk kegiatan berhasil ditambahkan.');
    }

    public function bentukDelete(int $id)
    {
        $this->bentukModel->delete($id);
        return redirect()->to('admin/pemilu/sisir-rw/bentuk')->with('success', 'Bentuk kegiatan berhasil dihapus.');
    }
}
