<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\MemberProductModel;
use App\Models\EducationModel;
use App\Models\WorkExperienceModel;
use App\Models\OrganizationModel;
use App\Models\SkillModel;
use App\Models\KegiatanAbsensiModel;

class MemberController extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
    }

    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses Direktori Anggota.');
        }

        $data = [
            'title'   => 'Direktori Anggota — Ngobrol Pikiran Indonesia',
            'members' => $this->memberModel->where('is_active', 1)
                                           ->orderBy('nama_lengkap', 'ASC')
                                           ->paginate(12),
            'pager'   => $this->memberModel->pager,
        ];

        return view('pages/members/index', $data);
    }

    public function show($id = null)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk melihat detail profil anggota.');
        }

        $member = $this->memberModel->find($id);

        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $productModel  = new MemberProductModel();
        $eduModel      = new EducationModel();
        $workModel     = new WorkExperienceModel();
        $orgModel      = new OrganizationModel();
        $skillModel    = new SkillModel();
        $absensiModel  = new KegiatanAbsensiModel();

        // Check if the logged-in user is viewing their own profile
        $userId = auth()->id();
        $isOwnProfile = ($member['user_id'] !== null && (int)$member['user_id'] === (int)$userId);

        $data = [
            'title'         => $member['nama_lengkap'] . ' — Ngobrol Pikiran Indonesia',
            'member'        => $member,
            'isOwnProfile'  => $isOwnProfile,
            'products'      => $productModel->where('member_id', $id)
                                            ->where('status', 'active')
                                            ->find(),
            'educations'    => $eduModel->where('member_id', $id)
                                        ->findAll(),
            'workExperiences' => $workModel->where('member_id', $id)
                                           ->findAll(),
            'organizations' => $orgModel->where('member_id', $id)
                                        ->findAll(),
            'skills'        => $skillModel->where('member_id', $id)
                                          ->findAll(),
            'kegiatanHistory' => $absensiModel->getByMember($id),
        ];

        return view('pages/members/detail', $data);
    }

    public function profile()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = auth()->id();
        $member = $this->memberModel->where('user_id', $userId)->first();

        if ($member) {
            return $this->show($member['id']);
        }

        // Jika belum ada data profil anggota yang tertaut
        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];
        $hasAdminRole = false;
        foreach ($userGroups as $grp) {
            if ($grp !== 'user') {
                $hasAdminRole = true;
                break;
            }
        }

        if ($hasAdminRole) {
            return redirect()->to(base_url('admin/dashboard'))->with('info', 'Akun Anda terdaftar sebagai pengelola.');
        }

        return redirect()->to(base_url('anggota'))->with('info', 'Data profil anggota Anda belum ditautkan.');
    }
}
