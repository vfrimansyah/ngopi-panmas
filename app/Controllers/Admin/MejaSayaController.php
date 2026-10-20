<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\MemberProductModel;
use App\Models\EducationModel;
use App\Models\WorkExperienceModel;
use App\Models\OrganizationModel;
use App\Models\SkillModel;
use App\Models\KegiatanAbsensiModel;
use App\Models\MutabaahItemModel;
use App\Models\MutabaahLogModel;
use App\Models\MemberIuranModel;
use App\Models\FinRekeningModel;

class MejaSayaController extends BaseController
{
    protected $memberModel;
    protected $mutabaahItemModel;
    protected $mutabaahLogModel;
    protected $iuranModel;

    public function __construct()
    {
        $this->memberModel       = new MemberModel();
        $this->mutabaahItemModel = new MutabaahItemModel();
        $this->mutabaahLogModel  = new MutabaahLogModel();
        $this->iuranModel        = new MemberIuranModel();
    }

    /**
     * Get member linked to currently authenticated user
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
     * Redirect default to profile
     */
    public function index()
    {
        return redirect()->to(base_url('admin/meja/profile'));
    }

    /**
     * View personal profile
     */
    public function profile()
    {
        $member = $this->getLinkedMember();

        if (!$member) {
            // Unlinked user account (e.g. initial superadmin)
            $db = \Config\Database::connect();
            $unlinkedMembers = $db->table('members m')
                ->select('m.id, m.nama_lengkap, m.kode_upa, m.status_anggota, m.user_id, u.username as linked_username')
                ->join('users u', 'u.id = m.user_id', 'left')
                ->where('m.is_active', 1)
                ->orderBy('m.nama_lengkap', 'ASC')
                ->get()
                ->getResultArray();

            return view('admin/meja/profile', [
                'title'           => 'Profil Saya — Meja Saya',
                'isLinked'        => false,
                'member'          => null,
                'unlinkedMembers' => $unlinkedMembers,
                'user'            => auth()->user(),
            ]);
        }

        $id = $member['id'];
        $productModel = new MemberProductModel();
        $eduModel     = new EducationModel();
        $workModel    = new WorkExperienceModel();
        $orgModel     = new OrganizationModel();
        $skillModel   = new SkillModel();
        $absensiModel      = new KegiatanAbsensiModel();
        $formResponseModel = new \App\Models\CustomFormResponseModel();

        $data = [
            'title'           => 'Profil Saya — ' . esc($member['nama_lengkap']),
            'isLinked'        => true,
            'member'          => $member,
            'products'        => $productModel->where('member_id', $id)->findAll(),
            'educations'      => $eduModel->where('member_id', $id)->findAll(),
            'workExperiences' => $workModel->where('member_id', $id)->findAll(),
            'organizations'   => $orgModel->where('member_id', $id)->findAll(),
            'skills'          => $skillModel->where('member_id', $id)->findAll(),
            'kegiatanHistory' => $absensiModel->getByMember($id),
            'surveyHistory'   => $formResponseModel->getResponsesByMember($id),
            'user'            => auth()->user(),
        ];

        return view('admin/meja/profile', $data);
    }

    /**
     * Update personal profile info
     */
    public function updateProfile()
    {
        $member = $this->getLinkedMember();
        if (!$member) {
            return redirect()->back()->with('error', 'Akun Anda belum ditautkan ke profil anggota.');
        }

        $validationRules = [
            'email'               => 'permit_empty|valid_email|max_length[255]',
            'handphone'           => 'permit_empty|max_length[30]',
            'alamat_lengkap'      => 'permit_empty|max_length[500]',
            'rt'                  => 'permit_empty|max_length[10]',
            'rw'                  => 'permit_empty|max_length[10]',
            'pekerjaan'           => 'permit_empty|max_length[100]',
            'nama_instansi'       => 'permit_empty|max_length[255]',
            'pendidikan_terakhir' => 'permit_empty|max_length[50]',
            'keahlian'            => 'permit_empty|max_length[255]',
            'hobi'                => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $updateData = [
            'email'               => trim((string)$this->request->getPost('email')),
            'handphone'           => trim((string)$this->request->getPost('handphone')),
            'alamat_lengkap'      => trim((string)$this->request->getPost('alamat_lengkap')),
            'rt'                  => trim((string)$this->request->getPost('rt')),
            'rw'                  => trim((string)$this->request->getPost('rw')),
            'pekerjaan'           => trim((string)$this->request->getPost('pekerjaan')),
            'nama_instansi'       => trim((string)$this->request->getPost('nama_instansi')),
            'pendidikan_terakhir' => trim((string)$this->request->getPost('pendidikan_terakhir')),
            'keahlian'            => trim((string)$this->request->getPost('keahlian')),
            'hobi'                => trim((string)$this->request->getPost('hobi')),
        ];

        // Handle photo upload if present
        $photo = $this->request->getFile('foto_profil');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/profile_photos', $newName);
            $updateData['foto_profil'] = 'profile_photos/' . $newName;
        }

        $this->memberModel->update($member['id'], $updateData);

        return redirect()->to(base_url('admin/meja/profile'))->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Link current user to a member record
     */
    public function linkMember()
    {
        $userId = auth()->id();
        $memberId = (int)$this->request->getPost('member_id');

        if (!$memberId) {
            return redirect()->back()->with('error', 'Pilih anggota yang valid.');
        }

        $targetMember = $this->memberModel->find($memberId);
        if (!$targetMember) {
            return redirect()->back()->with('error', 'Data anggota tidak ditemukan.');
        }

        if (!empty($targetMember['user_id']) && (int)$targetMember['user_id'] !== (int)$userId) {
            log_message('notice', "User ID {$userId} re-linked member ID {$memberId} from previous user ID {$targetMember['user_id']}");
        }

        $this->memberModel->update($memberId, [
            'user_id'        => $userId,
            'account_status' => 'active',
        ]);

        return redirect()->to(base_url('admin/meja/profile'))->with('success', 'Akun berhasil ditautkan ke data anggota: ' . $targetMember['nama_lengkap']);
    }

    /**
     * Mutabaah Yaumiyah Personal Page
     */
    public function mutabaah()
    {
        $member = $this->getLinkedMember();

        // Date selection (default today)
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $tanggal = date('Y-m-d');
        }

        if (!$member) {
            return view('admin/meja/mutabaah', [
                'title'    => 'Mutabaah Yaumiyah — Meja Saya',
                'isLinked' => false,
                'member'   => null,
                'tanggal'  => $tanggal,
            ]);
        }

        $timestamp = strtotime($tanggal);
        $tahun = (int)date('Y', $timestamp);
        $bulan = (int)date('n', $timestamp);

        // Active mutabaah items
        $items = $this->mutabaahItemModel
            ->where('is_active', 1)
            ->orderBy('urutan', 'ASC')
            ->findAll();

        // Get log for chosen date
        $currentLog = $this->mutabaahLogModel->getLogForDate($member['id'], $tanggal);

        // Get monthly recap
        $monthlyRecap = $this->mutabaahLogModel->getMonthlyRecap($member['id'], $tahun, $bulan);

        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('admin/meja/mutabaah', [
            'title'        => 'Mutabaah Yaumiyah — Meja Saya',
            'isLinked'     => true,
            'member'       => $member,
            'tanggal'      => $tanggal,
            'tahun'        => $tahun,
            'bulan'        => $bulan,
            'namaBulan'    => $bulanNames[$bulan],
            'items'        => $items,
            'currentLog'   => $currentLog,
            'monthlyRecap' => $monthlyRecap,
        ]);
    }

    /**
     * Save daily mutabaah log
     */
    public function saveMutabaah()
    {
        $member = $this->getLinkedMember();
        if (!$member) {
            return redirect()->back()->with('error', 'Akun Anda belum ditautkan ke profil anggota.');
        }

        $tanggal = $this->request->getPost('tanggal');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return redirect()->back()->with('error', 'Format tanggal tidak valid.');
        }

        $catatan = trim((string)$this->request->getPost('catatan'));
        $rawItems = $this->request->getPost('items') ?: [];

        $success = $this->mutabaahLogModel->saveDailyLog($member['id'], $tanggal, $catatan, $rawItems);

        if ($success) {
            return redirect()->to(base_url('admin/meja/mutabaah?tanggal=' . $tanggal))->with('success', 'Mutabaah tanggal ' . date('d/m/Y', strtotime($tanggal)) . ' berhasil disimpan.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan mutabaah.');
    }

    /**
     * Personal Iuran & Donation Status
     */
    public function iuran()
    {
        $member = $this->getLinkedMember();
        $tahun  = (int)($this->request->getGet('tahun') ?: date('Y'));
        if ($tahun < 2020 || $tahun > 2035) {
            $tahun = (int)date('Y');
        }

        if (!$member) {
            return view('admin/meja/iuran', [
                'title'    => 'Iuran Anggota — Meja Saya',
                'isLinked' => false,
                'member'   => null,
                'tahun'    => $tahun,
            ]);
        }

        $report  = $this->iuranModel->getAnnualReportForMember($member['id'], $tahun);
        $summary = $this->iuranModel->getMemberAnnualSummary($member['id'], $tahun);

        // Fetch bank accounts for donation/iuran transfer
        $rekeningModel = new FinRekeningModel();
        $rekeningList = $rekeningModel->where('is_active', 1)->findAll();

        return view('admin/meja/iuran', [
            'title'        => 'Iuran Saya — Meja Saya',
            'isLinked'     => true,
            'member'       => $member,
            'tahun'        => $tahun,
            'report'       => $report,
            'summary'      => $summary,
            'rekeningList' => $rekeningList,
        ]);
    }
}
