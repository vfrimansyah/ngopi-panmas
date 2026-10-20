<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegionUpaModel;
use App\Models\MemberModel;
use App\Models\MemberUpaHistoryModel;
use App\Models\UpaMentorHistoryModel;

class UpaController extends BaseController
{
    protected $upaModel;
    protected $memberModel;
    protected $historyModel;
    protected $mentorHistoryModel;

    public function __construct()
    {
        $this->upaModel = new RegionUpaModel();
        $this->memberModel = new MemberModel();
        $this->historyModel = new MemberUpaHistoryModel();
        $this->mentorHistoryModel = new UpaMentorHistoryModel();
    }

    private function checkWriteAccess()
    {
        $user = auth()->user();
        if (!$user || (!$user->inGroup('admin') && !$user->inGroup('kaderisasi'))) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Anda tidak memiliki otorisasi untuk melakukan tindakan ini.');
        }
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $kelurahanId = $this->request->getGet('kelurahan_id');
        $q = trim((string)$this->request->getGet('q'));

        $builder = $this->upaModel
            ->select('regions_upa.*, cms_region04_villages.name as kelurahan, cms_region03_districts.name as kecamatan, members.nama_lengkap as nama_pembimbing, members.status_anggota as jenjang_pembimbing')
            ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = regions_upa.kecamatan_id', 'left')
            ->join('members', 'members.id = regions_upa.pembimbing_id', 'left')
            ->orderBy('regions_upa.kode_upa', 'ASC');

        if (!empty($kelurahanId)) {
            $builder->where('regions_upa.kelurahan_id', $kelurahanId);
        }

        if (!empty($q)) {
            $builder->groupStart()
                ->like('regions_upa.kode_upa', $q)
                ->orLike('regions_upa.nama_group', $q)
                ->orLike('members.nama_lengkap', $q)
                ->orLike('cms_region04_villages.name', $q)
            ->groupEnd();
        }

        $rawUpas = $builder->findAll();

        $upas = [];
        $totalMembers = 0;
        foreach ($rawUpas as $upa) {
            // Hitung jumlah anggota aktif di UPA ini
            $memberCount = $this->memberModel->where('kode_upa', $upa['kode_upa'])->countAllResults();
            $upa['jumlah_anggota'] = $memberCount;
            $totalMembers += $memberCount;
            $upas[] = $upa;
        }

        // Ambil daftar kelurahan untuk filter dropdown
        $villages = $db->table('cms_region04_villages')
            ->select('id, name')
            ->where('district_id', '3276010') // Pancoran Mas or get all Depok villages
            ->orWhere('district_id LIKE', '3276%')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Check if current user has write access
        $user = auth()->user();
        $hasWriteAccess = $user && ($user->inGroup('admin') || $user->inGroup('kaderisasi'));

        $data = [
            'title'          => 'Manajemen Group UPA (Unit Pembinaan Anggota)',
            'upas'           => $upas,
            'villages'       => $villages,
            'selectedKel'    => $kelurahanId,
            'searchQuery'    => $q,
            'totalUpas'      => count($upas),
            'totalMembers'   => $totalMembers,
            'hasWriteAccess' => $hasWriteAccess
        ];

        return view('admin/upa/index', $data);
    }

    public function create()
    {
        $this->checkWriteAccess();
        $db = \Config\Database::connect();

        // Get districts from cms_region03_districts for Depok City (city_id = 3276)
        $districts = $db->table('cms_region03_districts')
            ->where('city_id', '3276')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get eligible mentors (Madya, Dewasa, Utama)
        $mentors = $this->memberModel
            ->select('id, nama_lengkap, status_anggota')
            ->whereIn('status_anggota', ['Madya', 'Dewasa', 'Utama'])
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title'     => 'Bentuk Group UPA Baru',
            'districts' => $districts,
            'mentors'   => $mentors
        ];

        return view('admin/upa/form', $data);
    }

    public function store()
    {
        $this->checkWriteAccess();

        $rules = [
            'kode_upa'     => 'required|is_unique[regions_upa.kode_upa]|min_length[3]|max_length[50]',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kodeUpa = trim((string)$this->request->getPost('kode_upa'));
        $namaGroup = trim((string)$this->request->getPost('nama_group')) ?: ('Group ' . $kodeUpa);
        $kecamatanId = $this->request->getPost('kecamatan_id');
        $kelurahanId = $this->request->getPost('kelurahan_id');
        $pembimbingId = $this->request->getPost('pembimbing_id') ?: null;
        $jadwalRutin = $this->request->getPost('jadwal_rutin') ?: null;
        $tempatPertemuan = $this->request->getPost('tempat_pertemuan') ?: null;
        $keterangan = $this->request->getPost('keterangan') ?: null;

        $pembimbingNama = null;
        if (!empty($pembimbingId)) {
            $mentor = $this->memberModel->find($pembimbingId);
            $pembimbingNama = $mentor ? $mentor['nama_lengkap'] : null;
        }

        $this->upaModel->insert([
            'kode_upa'           => $kodeUpa,
            'nama_group'         => $namaGroup,
            'kecamatan_id'       => $kecamatanId,
            'kelurahan_id'       => $kelurahanId,
            'kota_kabupaten_id'  => 184, // Kota Depok
            'pembimbing_id'      => $pembimbingId,
            'pembimbing_nama'    => $pembimbingNama,
            'jadwal_rutin'       => $jadwalRutin,
            'tempat_pertemuan'   => $tempatPertemuan,
            'keterangan'         => $keterangan,
            'is_active'          => 1,
        ]);

        // Jika pembimbing ditentukan, catat ke upa_mentor_histories
        if (!empty($pembimbingId)) {
            $this->mentorHistoryModel->insert([
                'kode_upa'        => $kodeUpa,
                'pembimbing_id'   => $pembimbingId,
                'pembimbing_nama' => $pembimbingNama,
                'tahun_mulai'     => date('Y'),
                'tahun_selesai'   => null,
                'periode'         => date('Y') . ' - Sekarang',
                'is_current'      => 1,
                'catatan'         => 'Penetapan awal pembimbing saat UPA dibentuk',
            ]);
        }

        return redirect()->to('admin/upa')->with('message', "Group UPA {$kodeUpa} berhasil dibentuk.");
    }

    public function edit($kodeUpa)
    {
        $this->checkWriteAccess();
        $db = \Config\Database::connect();

        $upa = $this->upaModel->find($kodeUpa);
        if (!$upa) {
            return redirect()->to('admin/upa')->with('error', 'Group UPA tidak ditemukan.');
        }

        // Get districts from cms_region03_districts for Depok City (city_id = 3276)
        $districts = $db->table('cms_region03_districts')
            ->where('city_id', '3276')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get villages for the selected district code
        $district = $db->table('cms_region03_districts')
            ->where('id', $upa['kecamatan_id'])
            ->get()
            ->getRowArray();

        $villages = [];
        if ($district) {
            $villages = $db->table('cms_region04_villages')
                ->where('district_id', $district['district_id'])
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get eligible mentors (Madya, Dewasa, Utama)
        $mentors = $this->memberModel
            ->select('id, nama_lengkap, status_anggota')
            ->whereIn('status_anggota', ['Madya', 'Dewasa', 'Utama'])
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $data = [
            'title'     => 'Edit Group UPA: ' . $kodeUpa,
            'upa'       => $upa,
            'districts' => $districts,
            'villages'  => $villages,
            'mentors'   => $mentors
        ];

        return view('admin/upa/form', $data);
    }

    public function update($kodeUpa)
    {
        $this->checkWriteAccess();

        $rules = [
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $namaGroup = trim((string)$this->request->getPost('nama_group')) ?: ('Group ' . $kodeUpa);
        $kecamatanId = $this->request->getPost('kecamatan_id');
        $kelurahanId = $this->request->getPost('kelurahan_id');
        $pembimbingId = $this->request->getPost('pembimbing_id') ?: null;
        $jadwalRutin = $this->request->getPost('jadwal_rutin') ?: null;
        $tempatPertemuan = $this->request->getPost('tempat_pertemuan') ?: null;
        $keterangan = $this->request->getPost('keterangan') ?: null;
        $isActive = $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1;

        // Cek apakah pembimbing berubah
        $oldUpa = $this->upaModel->find($kodeUpa);
        if ($oldUpa && $oldUpa['pembimbing_id'] != $pembimbingId) {
            $this->mentorHistoryModel->updateMentor($kodeUpa, $pembimbingId, [
                'tahun'   => date('Y'),
                'catatan' => 'Pergantian pembimbing/naqib via Edit UPA'
            ]);
        }

        $pembimbingNama = null;
        if (!empty($pembimbingId)) {
            $mentor = $this->memberModel->find($pembimbingId);
            $pembimbingNama = $mentor ? $mentor['nama_lengkap'] : null;
        }

        $this->upaModel->update($kodeUpa, [
            'nama_group'       => $namaGroup,
            'kecamatan_id'     => $kecamatanId,
            'kelurahan_id'     => $kelurahanId,
            'pembimbing_id'    => $pembimbingId,
            'pembimbing_nama'  => $pembimbingNama,
            'jadwal_rutin'     => $jadwalRutin,
            'tempat_pertemuan' => $tempatPertemuan,
            'keterangan'       => $keterangan,
            'is_active'        => $isActive,
        ]);

        return redirect()->to('admin/upa')->with('message', 'Data Group UPA berhasil diperbarui.');
    }

    public function delete($kodeUpa)
    {
        $this->checkWriteAccess();

        $db = \Config\Database::connect();
        
        // Tandai riwayat semua anggota UPA ini menjadi selesai/alumni
        $db->table('member_upa_histories')
            ->where('kode_upa', $kodeUpa)
            ->where('is_current', 1)
            ->update([
                'is_current'    => 0,
                'status'        => 'alumni',
                'tahun_selesai' => date('Y'),
                'periode'       => date('Y'),
                'catatan'       => 'Kelompok UPA dibubarkan / dihapus'
            ]);

        // Set members kode_upa to null
        $db->table('members')->where('kode_upa', $kodeUpa)->update(['kode_upa' => null]);

        $this->upaModel->delete($kodeUpa);

        return redirect()->to('admin/upa')->with('message', 'Group UPA berhasil dihapus.');
    }

    public function detail($kodeUpa)
    {
        $db = \Config\Database::connect();

        $upa = $this->upaModel
            ->select('regions_upa.*, cms_region04_villages.name as kelurahan, cms_region03_districts.name as kecamatan, members.nama_lengkap as nama_pembimbing, members.status_anggota as jenjang_pembimbing, members.handphone as hp_pembimbing')
            ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = regions_upa.kecamatan_id', 'left')
            ->join('members', 'members.id = regions_upa.pembimbing_id', 'left')
            ->find($kodeUpa);

        if (!$upa) {
            return redirect()->to('admin/upa')->with('error', 'Group UPA tidak ditemukan.');
        }

        // 1. Anggota Aktif di UPA ini
        $upaMembers = $this->memberModel
            ->where('kode_upa', $kodeUpa)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        // 2. Riwayat Alumni / Anggota yang Pernah Dibina di UPA ini
        $upaAlumni = $this->historyModel->getUpaAlumni($kodeUpa);

        // 3. Riwayat Pembimbing / Naqib di UPA ini
        $mentorHistories = $this->mentorHistoryModel->getMentorHistories($kodeUpa);

        // 4. Daftar Anggota Tanpa UPA (bisa dimasukkan)
        $unassignedMembers = $this->memberModel
            ->groupStart()
                ->where('kode_upa', null)
                ->orWhere('kode_upa', '')
            ->groupEnd()
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        // 5. Daftar UPA Lain (untuk modal pindah/transfer group)
        $otherUpas = $this->upaModel
            ->where('kode_upa !=', $kodeUpa)
            ->orderBy('kode_upa', 'ASC')
            ->findAll();

        // Check write access
        $user = auth()->user();
        $hasWriteAccess = $user && ($user->inGroup('admin') || $user->inGroup('kaderisasi'));

        $data = [
            'title'             => 'Kelola Group UPA: ' . $kodeUpa . ' - ' . ($upa['nama_group'] ?: $kodeUpa),
            'upa'               => $upa,
            'upaMembers'        => $upaMembers,
            'upaAlumni'         => $upaAlumni,
            'mentorHistories'   => $mentorHistories,
            'unassignedMembers' => $unassignedMembers,
            'otherUpas'         => $otherUpas,
            'hasWriteAccess'    => $hasWriteAccess
        ];

        return view('admin/upa/detail', $data);
    }

    public function addMember($kodeUpa)
    {
        $this->checkWriteAccess();

        $memberId = $this->request->getPost('member_id');
        $tahunMulai = $this->request->getPost('tahun_mulai') ?: date('Y');
        $catatan = $this->request->getPost('catatan') ?: 'Masuk ke dalam kelompok UPA';

        if (empty($memberId)) {
            return redirect()->back()->with('error', 'Silakan pilih anggota.');
        }

        try {
            $this->historyModel->assignMember($memberId, $kodeUpa, [
                'tahun_mulai' => $tahunMulai,
                'catatan'     => $catatan,
            ]);
            return redirect()->to('admin/upa/detail/' . $kodeUpa)->with('message', 'Anggota berhasil dimasukkan ke dalam group UPA dan riwayat tersimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memasukkan anggota: ' . $e->getMessage());
        }
    }

    public function transferMember($kodeUpa)
    {
        $this->checkWriteAccess();

        $memberId = $this->request->getPost('member_id');
        $targetKodeUpa = $this->request->getPost('target_kode_upa');
        $tahunPindah = $this->request->getPost('tahun_pindah') ?: date('Y');
        $catatan = $this->request->getPost('catatan') ?: "Mutasi / pindah dari {$kodeUpa} ke {$targetKodeUpa}";

        if (empty($memberId) || empty($targetKodeUpa)) {
            return redirect()->back()->with('error', 'Data anggota atau UPA tujuan tidak lengkap.');
        }

        try {
            $this->historyModel->assignMember($memberId, $targetKodeUpa, [
                'tahun_mulai'     => $tahunPindah,
                'previous_status' => 'pindah',
                'catatan'         => $catatan,
            ]);

            return redirect()->to('admin/upa/detail/' . $kodeUpa)->with('message', "Anggota berhasil dipindahkan ke Group {$targetKodeUpa}. Riwayat pemindahan otomatis tercatat.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memindahkan anggota: ' . $e->getMessage());
        }
    }

    public function removeMember($kodeUpa, $memberId)
    {
        $this->checkWriteAccess();

        $tahunSelesai = $this->request->getVar('tahun_selesai') ?: date('Y');
        $status = $this->request->getVar('status') ?: 'alumni';
        $catatan = $this->request->getVar('catatan') ?: 'Dikeluarkan dari kelompok UPA';

        try {
            $this->historyModel->removeMember($memberId, $kodeUpa, [
                'tahun_selesai' => $tahunSelesai,
                'status'        => $status,
                'catatan'       => $catatan,
            ]);

            return redirect()->to('admin/upa/detail/' . $kodeUpa)->with('message', 'Anggota berhasil dikeluarkan dari Group UPA dan dicatat dalam riwayat alumni.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengeluarkan anggota: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint API JSON untuk melihat riwayat UPA seorang anggota (misal untuk timeline modal)
     */
    public function memberHistory($memberId)
    {
        $member = $this->memberModel->find($memberId);
        if (!$member) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Anggota tidak ditemukan']);
        }

        $histories = $this->historyModel->getMemberHistories($memberId);

        return $this->response->setJSON([
            'member'    => [
                'id'             => $member['id'],
                'nama_lengkap'   => $member['nama_lengkap'],
                'status_anggota' => $member['status_anggota'],
                'kode_upa'       => $member['kode_upa'],
                'email'          => $member['email'],
                'handphone'      => $member['handphone']
            ],
            'histories' => $histories
        ]);
    }

    /**
     * Simpan / Tambah riwayat UPA manual untuk anggota (misal masa lalu)
     */
    public function saveMemberHistory()
    {
        $this->checkWriteAccess();

        $memberId = $this->request->getPost('member_id');
        $kodeUpa = trim((string)$this->request->getPost('kode_upa'));
        $namaGroup = trim((string)$this->request->getPost('nama_group')) ?: ('Group ' . $kodeUpa);
        $pembimbingNama = trim((string)$this->request->getPost('pembimbing_nama'));
        $tahunMulai = trim((string)$this->request->getPost('tahun_mulai'));
        $tahunSelesai = trim((string)$this->request->getPost('tahun_selesai'));
        $status = $this->request->getPost('status') ?: 'selesai';
        $isCurrent = $this->request->getPost('is_current') ? 1 : 0;
        $catatan = trim((string)$this->request->getPost('catatan'));

        if (empty($memberId) || empty($kodeUpa)) {
            return redirect()->back()->with('error', 'Member ID dan Kode UPA wajib diisi.');
        }

        $periode = $tahunMulai;
        if (!empty($tahunSelesai)) {
            $periode .= ' - ' . $tahunSelesai;
        } elseif ($isCurrent) {
            $periode .= ' - Sekarang';
        }

        $historyId = $this->request->getPost('id');

        $data = [
            'member_id'       => $memberId,
            'kode_upa'        => $kodeUpa,
            'nama_group'      => $namaGroup,
            'pembimbing_nama' => $pembimbingNama,
            'tahun_mulai'     => $tahunMulai ?: null,
            'tahun_selesai'   => $tahunSelesai ?: null,
            'periode'         => $periode,
            'status'          => $status,
            'is_current'      => $isCurrent,
            'catatan'         => $catatan,
        ];

        // Cari pembimbing ID jika nama cocok
        if (!empty($pembimbingNama)) {
            $p = $this->memberModel->like('nama_lengkap', $pembimbingNama)->first();
            if ($p) {
                $data['pembimbing_id'] = $p['id'];
            }
        }

        if (!empty($historyId)) {
            $this->historyModel->update($historyId, $data);
            $msg = 'Riwayat UPA berhasil diperbarui.';
        } else {
            if ($isCurrent) {
                // Nonaktifkan history lain
                $this->historyModel->where('member_id', $memberId)->set(['is_current' => 0])->update();
                // Update member's current upa
                $this->memberModel->update($memberId, ['kode_upa' => $kodeUpa]);
            }
            $this->historyModel->insert($data);
            $msg = 'Riwayat UPA berhasil ditambahkan.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Hapus satu entri riwayat UPA
     */
    public function deleteMemberHistory($id)
    {
        $this->checkWriteAccess();

        $history = $this->historyModel->find($id);
        if (!$history) {
            if ($this->request->isAJAX() || $this->request->getHeaderLine('accept') == 'application/json' || $this->request->getGet('ajax') || $this->request->getPost('ajax')) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Data riwayat UPA tidak ditemukan.'
                ]);
            }
            return redirect()->back()->with('error', 'Data riwayat UPA tidak ditemukan.');
        }

        $memberId   = $history['member_id'];
        $wasCurrent = ($history['is_current'] == 1);
        $kodeUpaDel = $history['kode_upa'];

        // Hapus entri riwayat
        $this->historyModel->delete($id);

        $member = $this->memberModel->find($memberId);
        $memberHadThisUpa = ($member && $member['kode_upa'] === $kodeUpaDel);

        $newKodeUpa = null;
        if ($wasCurrent || $memberHadThisUpa) {
            // Cari riwayat lain yang tersisa untuk anggota ini (diurutkan dari yang terbaru)
            $prevHistory = $this->historyModel
                ->where('member_id', $memberId)
                ->orderBy('tahun_mulai', 'DESC')
                ->orderBy('id', 'DESC')
                ->first();

            if ($prevHistory) {
                // Aktifkan riwayat sebelumnya
                $this->historyModel->update($prevHistory['id'], [
                    'is_current'    => 1,
                    'status'        => 'aktif',
                    'tahun_selesai' => null
                ]);
                $newKodeUpa = $prevHistory['kode_upa'];
                $this->memberModel->update($memberId, ['kode_upa' => $newKodeUpa]);
            } else {
                // Tidak ada riwayat lain yang tersisa
                $this->memberModel->update($memberId, ['kode_upa' => null]);
                $newKodeUpa = null;
            }
        } else {
            $newKodeUpa = $member ? $member['kode_upa'] : null;
        }

        $msg = "Riwayat UPA ({$kodeUpaDel}) berhasil dihapus.";
        if ($wasCurrent || $memberHadThisUpa) {
            if ($newKodeUpa) {
                $msg .= " Status UPA aktif anggota otomatis dikembalikan ke kelompok sebelumnya ({$newKodeUpa}).";
            } else {
                $msg .= " Anggota saat ini tidak memiliki kelompok aktif.";
            }
        }

        if ($this->request->isAJAX() || $this->request->getHeaderLine('accept') == 'application/json' || $this->request->getGet('ajax') || $this->request->getPost('ajax')) {
            return $this->response->setJSON([
                'status'       => 'success',
                'message'      => $msg,
                'new_kode_upa' => $newKodeUpa,
                'member_id'    => $memberId,
                'was_current'  => ($wasCurrent || $memberHadThisUpa)
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function getVillages()
    {
        $db = \Config\Database::connect();
        $districtId = $this->request->getGet('district_id');

        if (empty($districtId)) {
            return $this->response->setJSON([]);
        }

        // Get district code from id
        $district = $db->table('cms_region03_districts')
            ->where('id', $districtId)
            ->get()
            ->getRowArray();

        if (!$district) {
            return $this->response->setJSON([]);
        }

        $villages = $db->table('cms_region04_villages')
            ->where('district_id', $district['district_id'])
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($villages);
    }
}
