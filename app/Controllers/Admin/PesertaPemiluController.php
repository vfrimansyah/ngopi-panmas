<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemiluPesertaModel;
use App\Models\PemiluPartaiModel;

class PesertaPemiluController extends BaseController
{
    protected $pesertaModel;
    protected $partaiModel;

    public function __construct()
    {
        $this->pesertaModel = new PemiluPesertaModel();
        $this->partaiModel  = new PemiluPartaiModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        $selectedTahun = $this->request->getVar('tahun');
        
        // Ambil daftar tahun unik yang ada untuk dropdown filter
        $listTahun = $db->table('pemilu_peserta')
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();
            
        $query = $db->table('pemilu_peserta p')
            ->select('p.*, pt.nama_partai, pt.lambang_partai, pt.cakupan')
            ->join('pemilu_partai pt', 'pt.id = p.partai_id');
            
        if (!empty($selectedTahun)) {
            $query = $query->where('p.tahun', $selectedTahun);
        }
        
        $peserta = $query->orderBy('p.tahun', 'DESC')
            ->orderBy('p.no_urut', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'          => 'Daftar Peserta Pemilu',
            'peserta'        => $peserta,
            'listTahun'      => array_column($listTahun, 'tahun'),
            'selectedTahun'  => $selectedTahun,
            'pager'          => null
        ];

        $perms = $this->getPagePermissions('pemilu');
        $data = array_merge($data, [
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);

        return view('admin/pemilu_peserta/index', $data);
    }

    public function create()
    {
        $data = [
            'title'   => 'Tambah Peserta Pemilu',
            'partai'  => $this->partaiModel->orderBy('nama_partai', 'ASC')->findAll()
        ];

        return view('admin/pemilu_peserta/form', $data);
    }

    public function store()
    {
        $rules = $this->pesertaModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if this party is already registered for this election year
        $existing = $this->pesertaModel
            ->where('tahun', $this->request->getPost('tahun'))
            ->where('partai_id', $this->request->getPost('partai_id'))
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Partai ini sudah terdaftar sebagai peserta pemilu pada tahun tersebut.');
        }

        // Check if number is duplicate for this year
        $existingNoUrut = $this->pesertaModel
            ->where('tahun', $this->request->getPost('tahun'))
            ->where('no_urut', $this->request->getPost('no_urut'))
            ->first();

        if ($existingNoUrut) {
            return redirect()->back()->withInput()->with('error', 'Nomor urut tersebut sudah digunakan oleh partai lain pada pemilu tahun ini.');
        }

        $this->pesertaModel->save([
            'tahun'        => $this->request->getPost('tahun'),
            'partai_id'    => $this->request->getPost('partai_id'),
            'no_urut'      => $this->request->getPost('no_urut'),
            'ketua_partai' => $this->request->getPost('ketua_partai')
        ]);

        return redirect()->to('admin/pemilu/peserta')->with('success', 'Peserta pemilu berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $peserta = $this->pesertaModel->find($id);

        if (!$peserta) {
            return redirect()->to('admin/pemilu/peserta')->with('error', 'Data peserta pemilu tidak ditemukan.');
        }

        $data = [
            'title'   => 'Edit Peserta Pemilu',
            'peserta' => $peserta,
            'partai'  => $this->partaiModel->orderBy('nama_partai', 'ASC')->findAll()
        ];

        return view('admin/pemilu_peserta/form', $data);
    }

    public function update($id)
    {
        $peserta = $this->pesertaModel->find($id);

        if (!$peserta) {
            return redirect()->to('admin/pemilu/peserta')->with('error', 'Data peserta pemilu tidak ditemukan.');
        }

        $rules = $this->pesertaModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if this party is already registered for this election year (excluding self)
        $existing = $this->pesertaModel
            ->where('tahun', $this->request->getPost('tahun'))
            ->where('partai_id', $this->request->getPost('partai_id'))
            ->where('id !=', $id)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Partai ini sudah terdaftar sebagai peserta pemilu pada tahun tersebut.');
        }

        // Check if number is duplicate for this year (excluding self)
        $existingNoUrut = $this->pesertaModel
            ->where('tahun', $this->request->getPost('tahun'))
            ->where('no_urut', $this->request->getPost('no_urut'))
            ->where('id !=', $id)
            ->first();

        if ($existingNoUrut) {
            return redirect()->back()->withInput()->with('error', 'Nomor urut tersebut sudah digunakan oleh partai lain pada pemilu tahun ini.');
        }

        $this->pesertaModel->update($id, [
            'tahun'        => $this->request->getPost('tahun'),
            'partai_id'    => $this->request->getPost('partai_id'),
            'no_urut'      => $this->request->getPost('no_urut'),
            'ketua_partai' => $this->request->getPost('ketua_partai')
        ]);

        return redirect()->to('admin/pemilu/peserta')->with('success', 'Peserta pemilu berhasil diperbarui.');
    }

    public function delete($id)
    {
        $peserta = $this->pesertaModel->find($id);

        if ($peserta) {
            $this->pesertaModel->delete($id);
            return redirect()->to('admin/pemilu/peserta')->with('success', 'Peserta pemilu berhasil dihapus.');
        }

        return redirect()->to('admin/pemilu/peserta')->with('error', 'Data peserta pemilu tidak ditemukan.');
    }
}
