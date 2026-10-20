<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LembagaProfileModel;
use App\Models\MemberModel;

class LembagaController extends BaseController
{
    protected LembagaProfileModel $lembagaModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->lembagaModel = new LembagaProfileModel();
        $this->memberModel  = new MemberModel();
    }

    /**
     * Get member linked to current user
     */
    protected function getLinkedMember(): ?array
    {
        $userId = auth()->id();
        if (!$userId) {
            return null;
        }
        return $this->memberModel->where('user_id', $userId)->first();
    }

    /**
     * Check if user is admin
     */
    protected function isUserAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->inGroup('admin') || $user->inGroup('superadmin') || $user->inGroup('bph');
    }

    /**
     * List all lembaga for this user
     */
    public function index()
    {
        $userId  = auth()->id();
        $member  = $this->getLinkedMember();
        $isAdmin = $this->isUserAdmin();

        $lembagas = $this->lembagaModel->getByUser($userId, $member['id'] ?? null, $isAdmin);

        $data = [
            'title'        => 'Profil Lembaga & Dokumen Legalitas',
            'lembagas'     => $lembagas,
            'member'       => $member,
            'isAdmin'      => $isAdmin,
            'lembagaModel' => $this->lembagaModel,
        ];

        return view('admin/meja/lembaga/index', $data);
    }

    /**
     * Show form to create new lembaga
     */
    public function create()
    {
        $member = $this->getLinkedMember();

        $allMembers = $this->memberModel->select('id, nama_lengkap, handphone, status_anggota')
            ->where('is_active', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();

        // Default: Jawa Barat (32), Kota Depok (3276), Pancoran Mas (327601)
        $defaultProvId = '32';
        $defaultRegId  = '3276';
        $defaultDistId = '327601';

        $provinces = $db->table('reg_provinces')->orderBy('name', 'ASC')->get()->getResultArray();
        $regencies = $db->table('reg_regencies')->where('province_id', $defaultProvId)->orderBy('name', 'ASC')->get()->getResultArray();
        $districts = $db->table('reg_districts')->where('regency_id', $defaultRegId)->orderBy('name', 'ASC')->get()->getResultArray();
        $villages  = $db->table('reg_villages')->where('district_id', $defaultDistId)->orderBy('name', 'ASC')->get()->getResultArray();

        $data = [
            'title'        => 'Tambah Profil Yayasan / Lembaga',
            'lembaga'      => null,
            'member'       => $member,
            'allMembers'   => $allMembers,
            'provinces'    => $provinces,
            'regencies'    => $regencies,
            'districts'    => $districts,
            'villages'     => $villages,
            'selectedProv' => $defaultProvId,
            'selectedReg'  => $defaultRegId,
            'selectedDist' => $defaultDistId,
            'selectedVill' => '',
        ];

        return view('admin/meja/lembaga/form', $data);
    }

    /**
     * Store new lembaga
     */
    public function store()
    {
        $userId = auth()->id();
        $member = $this->getLinkedMember();

        $rules = [
            'nama_lembaga'  => 'required|min_length[3]|max_length[255]',
            'jenis_lembaga' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        $provId = $this->request->getPost('province_id');
        $regId  = $this->request->getPost('regency_id');
        $distId = $this->request->getPost('district_id');
        $villId = $this->request->getPost('village_id');

        // Resolve names for text storage (used in letters/templates)
        $namaKelurahan = trim((string)$this->request->getPost('kelurahan'));
        $namaKecamatan = trim((string)$this->request->getPost('kecamatan'));
        $namaKota      = trim((string)$this->request->getPost('kota'));

        if ($villId) {
            $vRow = $db->table('reg_villages')->where('id', $villId)->get()->getRowArray();
            if ($vRow) $namaKelurahan = $vRow['name'];
        }
        if ($distId) {
            $dRow = $db->table('reg_districts')->where('id', $distId)->get()->getRowArray();
            if ($dRow) $namaKecamatan = $dRow['name'];
        }
        if ($regId) {
            $rRow = $db->table('reg_regencies')->where('id', $regId)->get()->getRowArray();
            if ($rRow) $namaKota = $rRow['name'];
        }

        $data = [
            'user_id'            => $userId,
            'member_id'          => $member['id'] ?? null,
            'nama_lembaga'       => trim($this->request->getPost('nama_lembaga')),
            'jenis_lembaga'      => $this->request->getPost('jenis_lembaga'),
            'kategori_bidang'    => $this->request->getPost('kategori_bidang'),
            'alamat_lengkap'     => $this->request->getPost('alamat_lengkap'),
            'province_id'        => $provId ?: null,
            'regency_id'         => $regId ?: null,
            'district_id'        => $distId ?: null,
            'village_id'         => $villId ?: null,
            'kelurahan'          => $namaKelurahan,
            'kecamatan'          => $namaKecamatan ?: 'Pancoran Mas',
            'kota'               => $namaKota ?: 'Kota Depok',
            'kode_pos'           => $this->request->getPost('kode_pos'),
            'no_kontak'          => $this->request->getPost('no_kontak'),
            'email'              => $this->request->getPost('email'),
            'website'            => $this->request->getPost('website'),
            'nama_ketua'         => $this->request->getPost('nama_ketua'),
            'nik_ketua'          => $this->request->getPost('nik_ketua'),
            'hp_ketua'           => $this->request->getPost('hp_ketua'),
            'npwp_ketua'         => $this->request->getPost('npwp_ketua'),
            'nama_sekretaris'    => $this->request->getPost('nama_sekretaris'),
            'nama_bendahara'     => $this->request->getPost('nama_bendahara'),
            'no_akta_notaris'    => $this->request->getPost('no_akta_notaris'),
            'nama_notaris'       => $this->request->getPost('nama_notaris'),
            'tgl_akta'           => $this->request->getPost('tgl_akta') ?: null,
            'no_sk_kemenkumham'  => $this->request->getPost('no_sk_kemenkumham'),
            'npwp_lembaga'       => $this->request->getPost('npwp_lembaga'),
            'nib'                => $this->request->getPost('nib'),
            'nama_bank'          => $this->request->getPost('nama_bank'),
            'no_rekening'        => $this->request->getPost('no_rekening'),
            'atas_nama_rekening' => $this->request->getPost('atas_nama_rekening'),
        ];

        // Handle File Uploads
        $fileFields = [
            'file_logo',
            'file_ktp_ketua',
            'file_npwp_ketua',
            'file_npwp_lembaga',
            'file_akta',
            'file_sk_kemenkumham',
            'file_nib',
            'file_buku_rekening',
            'file_domisili',
            'file_susunan_pengurus',
        ];

        $uploadPath = FCPATH . 'uploads/lembaga';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $data[$field] = $newName;
            }
        }

        // Authorized members & public sharing
        $rawAuth = $this->request->getPost('authorized_members');
        $authMembers = [];
        if (!empty($rawAuth) && is_array($rawAuth)) {
            $authMembers = array_values(array_filter(array_map('strval', $rawAuth)));
        }
        $data['authorized_members'] = !empty($authMembers) ? json_encode($authMembers) : null;
        $data['is_public']          = $this->request->getPost('is_public') ? 1 : 0;

        $this->lembagaModel->insert($data);

        return redirect()->to(base_url('admin/meja/lembaga'))->with('success', 'Profil yayasan/lembaga berhasil disimpan lengkap dengan berkas legalitas.');
    }

    /**
     * Show form to edit existing lembaga
     */
    public function edit(int $id)
    {
        $lembaga = $this->lembagaModel->find($id);
        if (!$lembaga) {
            return redirect()->to(base_url('admin/meja/lembaga'))->with('error', 'Data lembaga tidak ditemukan.');
        }

        $userId  = auth()->id();
        $member  = $this->getLinkedMember();
        $isAdmin = $this->isUserAdmin();

        // Hanya owner pendaftar atau admin yang berhak mengedit data & berkas legalitas resmi
        if (!$this->lembagaModel->isOwner($lembaga, $userId, $member['id'] ?? null, $isAdmin)) {
            return redirect()->to(base_url('admin/meja/lembaga'))->with('error', 'Hanya pendaftar lembaga (Owner) atau Admin yang berhak mengubah berkas legalitas resmi yayasan ini.');
        }

        $allMembers = $this->memberModel->select('id, nama_lengkap, handphone, status_anggota')
            ->where('is_active', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();

        $selectedProv = $lembaga['province_id'] ?? null;
        $selectedReg  = $lembaga['regency_id'] ?? null;
        $selectedDist = $lembaga['district_id'] ?? null;
        $selectedVill = $lembaga['village_id'] ?? null;

        // Auto-match existing text if IDs not yet set
        if (!$selectedProv) {
            $selectedProv = '32'; // Default Jawa Barat
        }
        if (!$selectedReg && !empty($lembaga['kota'])) {
            $cleanKota = trim(str_ireplace(['Kota', 'Kabupaten', 'Kab.', 'Kab'], '', $lembaga['kota']));
            $matchedReg = $db->table('reg_regencies')
                ->where('province_id', $selectedProv)
                ->like('name', $cleanKota)
                ->get()->getRowArray();
            if ($matchedReg) {
                $selectedReg = $matchedReg['id'];
            }
        }
        if (!$selectedReg) {
            $selectedReg = '3276'; // Default Kota Depok
        }

        if (!$selectedDist && !empty($lembaga['kecamatan'])) {
            $cleanKec = trim(str_ireplace(['Kecamatan', 'Kec.', 'Kec'], '', $lembaga['kecamatan']));
            $matchedDist = $db->table('reg_districts')
                ->where('regency_id', $selectedReg)
                ->like('name', $cleanKec)
                ->get()->getRowArray();
            if ($matchedDist) {
                $selectedDist = $matchedDist['id'];
            }
        }
        if (!$selectedDist) {
            $selectedDist = '327601'; // Default Pancoran Mas
        }

        if (!$selectedVill && !empty($lembaga['kelurahan'])) {
            $cleanKel = trim(str_ireplace(['Kelurahan', 'Kel.', 'Kel', 'Desa'], '', $lembaga['kelurahan']));
            $matchedVill = $db->table('reg_villages')
                ->where('district_id', $selectedDist)
                ->like('name', $cleanKel)
                ->get()->getRowArray();
            if ($matchedVill) {
                $selectedVill = $matchedVill['id'];
            }
        }

        $provinces = $db->table('reg_provinces')->orderBy('name', 'ASC')->get()->getResultArray();
        $regencies = $db->table('reg_regencies')->where('province_id', $selectedProv)->orderBy('name', 'ASC')->get()->getResultArray();
        $districts = $db->table('reg_districts')->where('regency_id', $selectedReg)->orderBy('name', 'ASC')->get()->getResultArray();
        $villages  = $db->table('reg_villages')->where('district_id', $selectedDist)->orderBy('name', 'ASC')->get()->getResultArray();

        $data = [
            'title'        => 'Edit Profil Yayasan / Lembaga: ' . $lembaga['nama_lembaga'],
            'lembaga'      => $lembaga,
            'member'       => $member,
            'allMembers'   => $allMembers,
            'provinces'    => $provinces,
            'regencies'    => $regencies,
            'districts'    => $districts,
            'villages'     => $villages,
            'selectedProv' => $selectedProv,
            'selectedReg'  => $selectedReg,
            'selectedDist' => $selectedDist,
            'selectedVill' => $selectedVill,
        ];

        return view('admin/meja/lembaga/form', $data);
    }

    /**
     * Update existing lembaga
     */
    public function update(int $id)
    {
        $lembaga = $this->lembagaModel->find($id);
        if (!$lembaga) {
            return redirect()->to(base_url('admin/meja/lembaga'))->with('error', 'Data lembaga tidak ditemukan.');
        }

        $userId  = auth()->id();
        $member  = $this->getLinkedMember();
        $isAdmin = $this->isUserAdmin();

        if (!$this->lembagaModel->isOwner($lembaga, $userId, $member['id'] ?? null, $isAdmin)) {
            return redirect()->to(base_url('admin/meja/lembaga'))->with('error', 'Akses ditolak. Anda bukan pemilik lembaga ini.');
        }

        $rules = [
            'nama_lembaga'  => 'required|min_length[3]|max_length[255]',
            'jenis_lembaga' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        $provId = $this->request->getPost('province_id');
        $regId  = $this->request->getPost('regency_id');
        $distId = $this->request->getPost('district_id');
        $villId = $this->request->getPost('village_id');

        // Resolve names for text storage (used in letters/templates)
        $namaKelurahan = trim((string)$this->request->getPost('kelurahan'));
        $namaKecamatan = trim((string)$this->request->getPost('kecamatan'));
        $namaKota      = trim((string)$this->request->getPost('kota'));

        if ($villId) {
            $vRow = $db->table('reg_villages')->where('id', $villId)->get()->getRowArray();
            if ($vRow) $namaKelurahan = $vRow['name'];
        }
        if ($distId) {
            $dRow = $db->table('reg_districts')->where('id', $distId)->get()->getRowArray();
            if ($dRow) $namaKecamatan = $dRow['name'];
        }
        if ($regId) {
            $rRow = $db->table('reg_regencies')->where('id', $regId)->get()->getRowArray();
            if ($rRow) $namaKota = $rRow['name'];
        }

        $data = [
            'nama_lembaga'       => trim($this->request->getPost('nama_lembaga')),
            'jenis_lembaga'      => $this->request->getPost('jenis_lembaga'),
            'kategori_bidang'    => $this->request->getPost('kategori_bidang'),
            'alamat_lengkap'     => $this->request->getPost('alamat_lengkap'),
            'province_id'        => $provId ?: null,
            'regency_id'         => $regId ?: null,
            'district_id'        => $distId ?: null,
            'village_id'         => $villId ?: null,
            'kelurahan'          => $namaKelurahan,
            'kecamatan'          => $namaKecamatan ?: 'Pancoran Mas',
            'kota'               => $namaKota ?: 'Kota Depok',
            'kode_pos'           => $this->request->getPost('kode_pos'),
            'no_kontak'          => $this->request->getPost('no_kontak'),
            'email'              => $this->request->getPost('email'),
            'website'            => $this->request->getPost('website'),
            'nama_ketua'         => $this->request->getPost('nama_ketua'),
            'nik_ketua'          => $this->request->getPost('nik_ketua'),
            'hp_ketua'           => $this->request->getPost('hp_ketua'),
            'npwp_ketua'         => $this->request->getPost('npwp_ketua'),
            'nama_sekretaris'    => $this->request->getPost('nama_sekretaris'),
            'nama_bendahara'     => $this->request->getPost('nama_bendahara'),
            'no_akta_notaris'    => $this->request->getPost('no_akta_notaris'),
            'nama_notaris'       => $this->request->getPost('nama_notaris'),
            'tgl_akta'           => $this->request->getPost('tgl_akta') ?: null,
            'no_sk_kemenkumham'  => $this->request->getPost('no_sk_kemenkumham'),
            'npwp_lembaga'       => $this->request->getPost('npwp_lembaga'),
            'nib'                => $this->request->getPost('nib'),
            'nama_bank'          => $this->request->getPost('nama_bank'),
            'no_rekening'        => $this->request->getPost('no_rekening'),
            'atas_nama_rekening' => $this->request->getPost('atas_nama_rekening'),
        ];

        // Authorized members & public sharing
        $rawAuth = $this->request->getPost('authorized_members');
        $authMembers = [];
        if (!empty($rawAuth) && is_array($rawAuth)) {
            $authMembers = array_values(array_filter(array_map('strval', $rawAuth)));
        }
        $data['authorized_members'] = !empty($authMembers) ? json_encode($authMembers) : null;
        $data['is_public']          = $this->request->getPost('is_public') ? 1 : 0;

        // Handle File Uploads (replace if new file uploaded)
        $fileFields = [
            'file_logo',
            'file_ktp_ketua',
            'file_npwp_ketua',
            'file_npwp_lembaga',
            'file_akta',
            'file_sk_kemenkumham',
            'file_nib',
            'file_buku_rekening',
            'file_domisili',
            'file_susunan_pengurus',
        ];

        $uploadPath = FCPATH . 'uploads/lembaga';
        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Delete old file if exists
                if (!empty($lembaga[$field]) && file_exists($uploadPath . '/' . $lembaga[$field])) {
                    @unlink($uploadPath . '/' . $lembaga[$field]);
                }
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $data[$field] = $newName;
            }
        }

        $this->lembagaModel->update($id, $data);

        return redirect()->to(base_url('admin/meja/lembaga'))->with('success', 'Profil yayasan/lembaga berhasil diperbarui.');
    }

    /**
     * Delete lembaga
     */
    public function delete(int $id)
    {
        $lembaga = $this->lembagaModel->find($id);
        if (!$lembaga) {
            return redirect()->to(base_url('admin/meja/lembaga'))->with('error', 'Data tidak ditemukan.');
        }

        $userId  = auth()->id();
        $member  = $this->getLinkedMember();
        $isAdmin = $this->isUserAdmin();
        if (!$this->lembagaModel->isOwner($lembaga, $userId, $member['id'] ?? null, $isAdmin)) {
            return redirect()->to(base_url('admin/meja/lembaga'))->with('error', 'Akses ditolak. Hanya pendaftar lembaga (Owner) atau Admin yang dapat menghapus lembaga ini.');
        }

        // Delete uploaded files
        $uploadPath = FCPATH . 'uploads/lembaga';
        $fileFields = [
            'file_logo', 'file_ktp_ketua', 'file_npwp_ketua', 'file_npwp_lembaga',
            'file_akta', 'file_sk_kemenkumham', 'file_nib', 'file_buku_rekening',
            'file_domisili', 'file_susunan_pengurus',
        ];
        foreach ($fileFields as $field) {
            if (!empty($lembaga[$field]) && file_exists($uploadPath . '/' . $lembaga[$field])) {
                @unlink($uploadPath . '/' . $lembaga[$field]);
            }
        }

        $this->lembagaModel->delete($id);
        return redirect()->to(base_url('admin/meja/lembaga'))->with('success', 'Data lembaga berhasil dihapus.');
    }

    /**
     * AJAX endpoint to fetch lembaga details for proposal generator
     */
    public function getAjax(int $id)
    {
        $lembaga = $this->lembagaModel->find($id);
        if (!$lembaga) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Lembaga tidak ditemukan']);
        }
        return $this->response->setJSON(['status' => 'success', 'data' => $lembaga]);
    }

    /**
     * AJAX: Get regencies by province_id
     */
    public function getRegencies()
    {
        $provinceId = $this->request->getGet('province_id');
        $db = \Config\Database::connect();
        $data = $db->table('reg_regencies')
            ->where('province_id', $provinceId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($data);
    }

    /**
     * AJAX: Get districts by regency_id
     */
    public function getDistricts()
    {
        $regencyId = $this->request->getGet('regency_id');
        $db = \Config\Database::connect();
        $data = $db->table('reg_districts')
            ->where('regency_id', $regencyId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($data);
    }

    /**
     * AJAX: Get villages by district_id
     */
    public function getVillages()
    {
        $districtId = $this->request->getGet('district_id');
        $db = \Config\Database::connect();
        $data = $db->table('reg_villages')
            ->where('district_id', $districtId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($data);
    }
}
