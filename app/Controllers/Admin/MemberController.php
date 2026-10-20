<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\RegionUpaModel;
use App\Models\MemberAddressModel;
use App\Models\EducationModel;
use App\Models\OrganizationModel;
use App\Models\WorkExperienceModel;
use App\Models\SkillModel;
use App\Models\JobFieldModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class MemberController extends BaseController
{
    protected $memberModel;
    protected $regionModel;
    protected $addressModel;
    protected $educationModel;
    protected $organizationModel;
    protected $workExperienceModel;
    protected $skillModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->regionModel = new RegionUpaModel();
        $this->addressModel = new MemberAddressModel();
        $this->educationModel = new EducationModel();
        $this->organizationModel = new OrganizationModel();
        $this->workExperienceModel = new WorkExperienceModel();
        $this->skillModel = new SkillModel();
    }

    public function index()
    {
        $search        = $this->request->getVar('search');
        $statusAnggota = $this->request->getVar('status_anggota');
        $kodeUpa       = $this->request->getVar('kode_upa');
        $accountFilter = $this->request->getVar('account_filter');
        $gender        = $this->request->getVar('gender');

        $query = $this->memberModel
            ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan, cms_region02_cities.name AS kota_kabupaten')
            ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left')
            ->join('cms_region02_cities', 'cms_region02_cities.id = members.kota_kabupaten_id', 'left');

        if (!empty($search)) {
            $query = $query->like('members.nama_lengkap', $search);
        }
        // Default to 'pelopor' on initial load
        if ($statusAnggota === null) {
            $statusAnggota = 'pelopor';
        }

        if ($statusAnggota === 'pelopor') {
            $query = $query->whereIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama']);
        } elseif ($statusAnggota === 'non_pelopor') {
            $query = $query->groupStart()
                ->whereNotIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama'])
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota === 'all') {
            // No filter (show all)
        } elseif ($statusAnggota === 'Belum Tarbiyah') {
            $query = $query->groupStart()
                ->where('members.status_anggota', 'Belum Tarbiyah')
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif (!empty($statusAnggota)) {
            $query = $query->where('members.status_anggota', $statusAnggota);
        }
        if (!empty($gender)) {
            $query = $query->where('members.jenis_kelamin', $gender);
        }
        if (!empty($kodeUpa)) {
            $query = $query->where('members.kode_upa', $kodeUpa);
        }
        if (!empty($accountFilter)) {
            $query = $query->where('members.account_status', $accountFilter);
        }

        $data = [
            'title'          => 'Kelola Anggota - Admin',
            'members'        => $query->orderBy('members.created_at', 'DESC')->findAll(),
            'pager'          => null,
            'regions'        => $this->regionModel
                ->select('regions_upa.*, cms_region04_villages.name AS kelurahan')
                ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
                ->findAll(),
            'search'         => $search,
            'selected_status' => $statusAnggota,
            'selected_gender' => $gender,
            'selected_upa'   => $kodeUpa,
            'account_filter' => $accountFilter,
        ];

        // ── Resolve per-page permissions for current user ──────────────────────
        $permModel = new \App\Models\RolePermissionModel();
        $canCreate = false;
        $canUpdate = false;
        $canDelete = false;

        $user = auth()->user();
        if ($user) {
            $groups = $user->getGroups();
            foreach ($groups as $grp) {
                $perms = $permModel->getByRole($grp);
                if (!empty($perms['members'])) {
                    if ($perms['members']['can_create']) $canCreate = true;
                    if ($perms['members']['can_update']) $canUpdate = true;
                    if ($perms['members']['can_delete']) $canDelete = true;
                }
            }
            // Admin always has full access
            if (in_array('admin', $groups)) {
                $canCreate = $canUpdate = $canDelete = true;
            }
        }
        // ────────────────────────────────────────────────────────────────────────

        $data['can_create'] = $canCreate;
        $data['can_update'] = $canUpdate;
        $data['can_delete'] = $canDelete;

        return view('admin/members/index', $data);
    }

    public function create()
    {
        $potentialSpouses = $this->memberModel
            ->select('id, nama_lengkap, jenis_kelamin')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $potentialFathers = $this->memberModel
            ->select('id, nama_lengkap')
            ->where('jenis_kelamin', 'L')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $potentialMothers = $this->memberModel
            ->select('id, nama_lengkap')
            ->where('jenis_kelamin', 'P')
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $parentId = $this->request->getVar('parent_id');
        $prefilledFatherId = null;
        $prefilledMotherId = null;

        if ($parentId) {
            $parent = $this->memberModel->find($parentId);
            if ($parent) {
                if ($parent['jenis_kelamin'] === 'L') {
                    $prefilledFatherId = $parent['id'];
                } else {
                    $prefilledMotherId = $parent['id'];
                }

                // If the parent has a spouse, prefill the other parent as well
                if ($parent['spouse_member_id'] && $parent['spouse_pairing_status'] === 'confirmed') {
                    $spouse = $this->memberModel->find($parent['spouse_member_id']);
                    if ($spouse) {
                        if ($spouse['jenis_kelamin'] === 'L') {
                            $prefilledFatherId = $spouse['id'];
                        } else {
                            $prefilledMotherId = $spouse['id'];
                        }
                    }
                }
            }
        }

        $jobFields = (new JobFieldModel())->orderBy('name', 'ASC')->findAll();

        return view('admin/members/form', [
            'title' => 'Tambah Anggota - Admin',
            'member' => null,
            'spouse' => null,
            'potentialSpouses' => $potentialSpouses,
            'potentialFathers' => $potentialFathers,
            'potentialMothers' => $potentialMothers,
            'prefilledFatherId' => $prefilledFatherId,
            'prefilledMotherId' => $prefilledMotherId,
            'regions' => $this->regionModel
                ->select('regions_upa.*, cms_region04_villages.name AS kelurahan')
                ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
                ->findAll(),
            'children' => [],
            'addresses' => [],
            'educations' => [],
            'organizations' => [],
            'workExperiences' => [],
            'skills' => [],
            'jobFields' => $jobFields,
            'upaHistories' => [],
        ]);
    }

    public function store()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[255]',
            'jenis_kelamin' => 'permit_empty|in_list[L,P]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newSpouseId = $this->request->getPost('spouse_member_id') ?: null;

        $dbData = [
            'user_id' => null,
            'account_status' => $this->request->getPost('account_status') ?: 'no_account',
            'father_member_id' => $this->request->getPost('father_member_id') ?: null,
            'mother_member_id' => $this->request->getPost('mother_member_id') ?: null,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'handphone' => $this->request->getPost('handphone'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'usia_range' => $this->request->getPost('usia_range') ?: null,
            'status_anggota' => $this->request->getPost('status_anggota') ?: null,
            'kode_upa' => $this->request->getPost('kode_upa') ?: null,
            'status_perkawinan' => $this->request->getPost('status_perkawinan') ?: 'Lajang',
            'spouse_member_id' => $newSpouseId,
            'spouse_pairing_status' => $newSpouseId ? 'confirmed' : 'unmatched',
            'alamat_lengkap' => $this->request->getPost('alamat_lengkap'),
            'rt' => $this->request->getPost('rt'),
            'rw' => $this->request->getPost('rw'),
            'kelurahan_id' => $this->request->getPost('kelurahan_id') ?: null,
            'kecamatan_id' => $this->request->getPost('kecamatan_id') ?: null,
            'kota_kabupaten_id' => $this->request->getPost('kota_kabupaten_id') ?: null,
            'is_active' => $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : 1,
        ];

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->memberModel->save($dbData);
            $newId = $this->memberModel->getInsertID();

            if ($newId) {
                $this->syncMemberHistories($newId);

                // Catat ke riwayat UPA jika kode_upa terisi
                if (!empty($dbData['kode_upa'])) {
                    (new \App\Models\MemberUpaHistoryModel())->assignMember($newId, $dbData['kode_upa'], [
                        'tahun_mulai' => date('Y'),
                        'catatan'     => 'Penetapan awal saat pendaftaran anggota baru',
                    ]);
                }
            }

            // Set new spouse's reference
            if (!empty($newSpouseId) && $newId) {
                $this->memberModel->update($newSpouseId, [
                    'spouse_member_id' => $newId,
                    'spouse_pairing_status' => 'confirmed'
                ]);
            }
            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }

        return redirect()->to('/admin/members')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit($id = null)
    {
        $member = $this->memberModel->find($id);
        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $spouse = null;
        if (!empty($member['spouse_member_id'])) {
            $spouse = $this->memberModel->find($member['spouse_member_id']);
        }

        $potentialSpouses = $this->memberModel
            ->select('id, nama_lengkap, jenis_kelamin')
            ->where('id !=', $id)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $potentialFathers = $this->memberModel
            ->select('id, nama_lengkap')
            ->where('jenis_kelamin', 'L')
            ->where('id !=', $id)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        $potentialMothers = $this->memberModel
            ->select('id, nama_lengkap')
            ->where('jenis_kelamin', 'P')
            ->where('id !=', $id)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        // Get linked children
        $children = $this->memberModel
            ->groupStart()
                ->where('father_member_id', $id)
                ->orWhere('mother_member_id', $id)
            ->groupEnd()
            ->orderBy('tanggal_lahir', 'ASC')
            ->findAll();

        $addresses = $this->addressModel
            ->select('member_addresses.*, cms_region02_cities.name AS city_name, cms_region03_districts.name AS district_name, cms_region04_villages.name AS village_name')
            ->join('cms_region02_cities', 'cms_region02_cities.id = member_addresses.kota_kabupaten_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = member_addresses.kecamatan_id', 'left')
            ->join('cms_region04_villages', 'cms_region04_villages.id = member_addresses.kelurahan_id', 'left')
            ->where('member_id', $id)
            ->findAll();

        $educations = $this->educationModel->where('member_id', $id)->orderBy('id', 'ASC')->findAll();
        $organizations = $this->organizationModel->where('member_id', $id)->orderBy('id', 'ASC')->findAll();
        $workExperiences = $this->workExperienceModel->where('member_id', $id)->orderBy('id', 'ASC')->findAll();
        $skills = $this->skillModel->where('member_id', $id)->orderBy('id', 'ASC')->findAll();

        $jobFields = (new JobFieldModel())->orderBy('name', 'ASC')->findAll();
        $upaHistories = (new \App\Models\MemberUpaHistoryModel())->getMemberHistories($id);

        return view('admin/members/form', [
            'title' => 'Edit Anggota - Admin',
            'member' => $member,
            'spouse' => $spouse,
            'potentialSpouses' => $potentialSpouses,
            'potentialFathers' => $potentialFathers,
            'potentialMothers' => $potentialMothers,
            'regions' => $this->regionModel
                ->select('regions_upa.*, cms_region04_villages.name AS kelurahan')
                ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
                ->findAll(),
            'children' => $children,
            'addresses' => $addresses,
            'educations' => $educations,
            'organizations' => $organizations,
            'workExperiences' => $workExperiences,
            'skills' => $skills,
            'jobFields' => $jobFields,
            'upaHistories' => $upaHistories,
        ]);
    }

    public function update($id = null)
    {
        $member = $this->memberModel->find($id);
        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[255]',
            'jenis_kelamin' => 'permit_empty|in_list[L,P]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $oldSpouseId = $member['spouse_member_id'];
        $newSpouseId = $this->request->getPost('spouse_member_id') ?: null;

        $dbData = [
            'id' => $id,
            'account_status' => $this->request->getPost('account_status') ?: $member['account_status'],
            'father_member_id' => $this->request->getPost('father_member_id') ?: null,
            'mother_member_id' => $this->request->getPost('mother_member_id') ?: null,
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'handphone' => $this->request->getPost('handphone'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'usia_range' => $this->request->getPost('usia_range') ?: $member['usia_range'],
            'status_anggota' => $this->request->getPost('status_anggota') ?: null,
            'kode_upa' => $this->request->getPost('kode_upa') ?: null,
            'status_perkawinan' => $this->request->getPost('status_perkawinan'),
            'spouse_member_id' => $newSpouseId,
            'spouse_pairing_status' => $newSpouseId ? 'confirmed' : 'unmatched',
            'alamat_lengkap' => $this->request->getPost('alamat_lengkap'),
            'rt' => $this->request->getPost('rt'),
            'rw' => $this->request->getPost('rw'),
            'kelurahan_id' => $this->request->getPost('kelurahan_id') ?: null,
            'kecamatan_id' => $this->request->getPost('kecamatan_id') ?: null,
            'kota_kabupaten_id' => $this->request->getPost('kota_kabupaten_id') ?: null,
            'is_active' => $this->request->getPost('is_active') !== null ? (int)$this->request->getPost('is_active') : $member['is_active'],
        ];

        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            $this->memberModel->save($dbData);

            $this->syncMemberHistories($id);

            // Cek apakah UPA berubah
            if ($member['kode_upa'] !== $dbData['kode_upa']) {
                $upaHistoryModel = new \App\Models\MemberUpaHistoryModel();
                if (!empty($dbData['kode_upa'])) {
                    $upaHistoryModel->assignMember($id, $dbData['kode_upa'], [
                        'tahun_mulai' => date('Y'),
                        'catatan'     => 'Perubahan kelompok UPA dari formulir edit profil anggota',
                    ]);
                } else {
                    $upaHistoryModel->removeMember($id, $member['kode_upa'], [
                        'tahun_selesai' => date('Y'),
                        'status'        => 'alumni',
                        'catatan'       => 'Dikeluarkan dari kelompok UPA via formulir edit profil anggota',
                    ]);
                }
            }

            // If spouse changed
            if ($oldSpouseId != $newSpouseId) {
                // Clear old spouse's reference
                if (!empty($oldSpouseId)) {
                    $this->memberModel->update($oldSpouseId, [
                        'spouse_member_id' => null,
                        'spouse_pairing_status' => 'unmatched'
                    ]);
                }
                // Set new spouse's reference
                if (!empty($newSpouseId)) {
                    $this->memberModel->update($newSpouseId, [
                        'spouse_member_id' => $id,
                        'spouse_pairing_status' => 'confirmed'
                    ]);
                }
            }
            $db->transCommit();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }

        return redirect()->to('/admin/members')->with('success', 'Anggota berhasil diperbarui.');
    }

    public function delete($id = null)
    {
        $member = $this->memberModel->find($id);
        if (!$member) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $this->memberModel->delete($id);

        return redirect()->to('/admin/members')->with('success', 'Anggota berhasil dihapus.');
    }

    private function syncMemberHistories($memberId)
    {
        // 1. Alamat Tambahan
        $addrModel = new \App\Models\MemberAddressModel();
        $addrIds = $this->request->getPost('addresses[id]') ?: [];
        $addrLabels = $this->request->getPost('addresses[label]') ?: [];
        $addrAlamats = $this->request->getPost('addresses[alamat_lengkap]') ?: [];
        $addrRTs = $this->request->getPost('addresses[rt]') ?: [];
        $addrRWs = $this->request->getPost('addresses[rw]') ?: [];
        $addrCities = $this->request->getPost('addresses[kota_kabupaten_id]') ?: [];
        $addrDistricts = $this->request->getPost('addresses[kecamatan_id]') ?: [];
        $addrVillages = $this->request->getPost('addresses[kelurahan_id]') ?: [];

        $existingAddrs = $addrModel->where('member_id', $memberId)->findAll();
        $existingAddrIds = array_column($existingAddrs, 'id');
        $addrsToDelete = array_diff($existingAddrIds, array_filter($addrIds));
        if (!empty($addrsToDelete)) {
            $addrModel->delete($addrsToDelete);
        }
        for ($i = 0; $i < count($addrAlamats); $i++) {
            if (empty($addrAlamats[$i])) continue;
            $data = [
                'member_id' => $memberId,
                'label' => $addrLabels[$i] ?? '',
                'alamat_lengkap' => $addrAlamats[$i],
                'rt' => $addrRTs[$i] ?? '',
                'rw' => $addrRWs[$i] ?? '',
                'kota_kabupaten_id' => !empty($addrCities[$i]) ? $addrCities[$i] : null,
                'kecamatan_id' => !empty($addrDistricts[$i]) ? $addrDistricts[$i] : null,
                'kelurahan_id' => !empty($addrVillages[$i]) ? $addrVillages[$i] : null,
            ];
            $currentId = $addrIds[$i] ?? null;
            if ($currentId && in_array($currentId, $existingAddrIds)) {
                $addrModel->update($currentId, $data);
            } else {
                $addrModel->insert($data);
            }
        }

        // 2. Riwayat Pendidikan
        $eduModel = new \App\Models\EducationModel();
        $eduIds = $this->request->getPost('educations[id]') ?: [];
        $eduJenjang = $this->request->getPost('educations[jenjang]') ?: [];
        $eduInstitusi = $this->request->getPost('educations[nama_institusi]') ?: [];
        $eduTahun = $this->request->getPost('educations[rentang_tahun]') ?: [];

        $existingEdus = $eduModel->where('member_id', $memberId)->findAll();
        $existingEduIds = array_column($existingEdus, 'id');
        $edusToDelete = array_diff($existingEduIds, array_filter($eduIds));
        if (!empty($edusToDelete)) {
            $eduModel->delete($edusToDelete);
        }
        for ($i = 0; $i < count($eduInstitusi); $i++) {
            if (empty($eduInstitusi[$i])) continue;
            $data = [
                'member_id' => $memberId,
                'jenjang' => $eduJenjang[$i] ?? '',
                'nama_institusi' => $eduInstitusi[$i],
                'rentang_tahun' => $eduTahun[$i] ?? '',
            ];
            $currentId = $eduIds[$i] ?? null;
            if ($currentId && in_array($currentId, $existingEduIds)) {
                $eduModel->update($currentId, $data);
            } else {
                $eduModel->insert($data);
            }
        }

        // 3. Pengalaman Organisasi
        $orgModel = new \App\Models\OrganizationModel();
        $orgIds = $this->request->getPost('organizations[id]') ?: [];
        $orgNama = $this->request->getPost('organizations[nama_organisasi]') ?: [];
        $orgJabatan = $this->request->getPost('organizations[jabatan]') ?: [];
        $orgTahun = $this->request->getPost('organizations[rentang_tahun]') ?: [];

        $existingOrgs = $orgModel->where('member_id', $memberId)->findAll();
        $existingOrgIds = array_column($existingOrgs, 'id');
        $orgsToDelete = array_diff($existingOrgIds, array_filter($orgIds));
        if (!empty($orgsToDelete)) {
            $orgModel->delete($orgsToDelete);
        }
        for ($i = 0; $i < count($orgNama); $i++) {
            if (empty($orgNama[$i])) continue;
            $data = [
                'member_id' => $memberId,
                'nama_organisasi' => $orgNama[$i],
                'jabatan' => $orgJabatan[$i] ?? '',
                'rentang_tahun' => $orgTahun[$i] ?? '',
            ];
            $currentId = $orgIds[$i] ?? null;
            if ($currentId && in_array($currentId, $existingOrgIds)) {
                $orgModel->update($currentId, $data);
            } else {
                $orgModel->insert($data);
            }
        }

        // 4. Pengalaman Kerja
        $workModel = new \App\Models\WorkExperienceModel();
        $workIds = $this->request->getPost('work_experiences[id]') ?: [];
        $workJabatan = $this->request->getPost('work_experiences[jabatan]') ?: [];
        $workPerusahaan = $this->request->getPost('work_experiences[nama_perusahaan]') ?: [];
        $workBidangId = $this->request->getPost('work_experiences[bidang_pekerjaan_id]') ?: [];
        $workPendapatan = $this->request->getPost('work_experiences[pendapatan_range]') ?: [];
        $workAlamat = $this->request->getPost('work_experiences[alamat_kerja]') ?: [];
        $workTahun = $this->request->getPost('work_experiences[rentang_tahun]') ?: [];
 
        $existingWorks = $workModel->where('member_id', $memberId)->findAll();
        $existingWorkIds = array_column($existingWorks, 'id');
        $worksToDelete = array_diff($existingWorkIds, array_filter($workIds));
        if (!empty($worksToDelete)) {
            $workModel->delete($worksToDelete);
        }
        for ($i = 0; $i < count($workPerusahaan); $i++) {
            if (empty($workPerusahaan[$i])) continue;
            $data = [
                'member_id' => $memberId,
                'jabatan' => $workJabatan[$i] ?? '',
                'nama_perusahaan' => $workPerusahaan[$i],
                'bidang_pekerjaan_id' => !empty($workBidangId[$i]) ? (int)$workBidangId[$i] : null,
                'pendapatan_range' => $workPendapatan[$i] ?? '',
                'alamat_kerja' => $workAlamat[$i] ?? '',
                'rentang_tahun' => $workTahun[$i] ?? '',
            ];
            $currentId = $workIds[$i] ?? null;
            if ($currentId && in_array($currentId, $existingWorkIds)) {
                $workModel->update($currentId, $data);
            } else {
                $workModel->insert($data);
            }
        }

        // 5. Kemampuan/Keahlian
        $skillModel = new \App\Models\SkillModel();
        $skillIds = $this->request->getPost('skills[id]') ?: [];
        $skillNama = $this->request->getPost('skills[nama_keahlian]') ?: [];
        $skillKet = $this->request->getPost('skills[keterangan]') ?: [];

        $existingSkills = $skillModel->where('member_id', $memberId)->findAll();
        $existingSkillIds = array_column($existingSkills, 'id');
        $skillsToDelete = array_diff($existingSkillIds, array_filter($skillIds));
        if (!empty($skillsToDelete)) {
            $skillModel->delete($skillsToDelete);
        }
        for ($i = 0; $i < count($skillNama); $i++) {
            if (empty($skillNama[$i])) continue;
            $data = [
                'member_id' => $memberId,
                'nama_keahlian' => $skillNama[$i],
                'keterangan' => $skillKet[$i] ?? '',
            ];
            $currentId = $skillIds[$i] ?? null;
            if ($currentId && in_array($currentId, $existingSkillIds)) {
                $skillModel->update($currentId, $data);
            } else {
                $skillModel->insert($data);
            }
        }

        // Keep the main members table in sync with the latest/highest values
        $latestWork = $workModel->where('member_id', $memberId)->orderBy('id', 'DESC')->first();
        $educations = $eduModel->where('member_id', $memberId)->findAll();
        $skills = $skillModel->where('member_id', $memberId)->findAll();

        // 1. Determine highest education
        $highestEdu = null;
        if (!empty($educations)) {
            $eduOrder = [
                'SD' => 1,
                'SMP' => 2,
                'SMA/SMK' => 3,
                'D3' => 4,
                'S1' => 5,
                'S2' => 6,
                'S3' => 7,
            ];
            $highestOrder = 0;
            foreach ($educations as $edu) {
                $jen = $edu['jenjang'] ?? '';
                $order = $eduOrder[$jen] ?? 0;
                if ($order > $highestOrder) {
                    $highestOrder = $order;
                    $highestEdu = $jen;
                }
            }
        }

        // 2. Aggregate skills
        $skillsList = [];
        foreach ($skills as $sk) {
            if (!empty($sk['nama_keahlian'])) {
                $skillsList[] = trim($sk['nama_keahlian']);
            }
        }
        $keahlianText = implode(', ', $skillsList);

        // 3. Find matching bidang_pekerjaan name
        $bidangPekerjaanName = null;
        if ($latestWork && !empty($latestWork['bidang_pekerjaan_id'])) {
            $jf = (new \App\Models\JobFieldModel())->find($latestWork['bidang_pekerjaan_id']);
            if ($jf) {
                $bidangPekerjaanName = $jf['name'];
            }
        }

        // 4. Update the main members table
        $updateData = [
            'pekerjaan' => $latestWork ? $latestWork['jabatan'] : null,
            'nama_instansi' => $latestWork ? $latestWork['nama_perusahaan'] : null,
            'alamat_kerja' => $latestWork ? $latestWork['alamat_kerja'] : null,
            'bidang_pekerjaan' => $bidangPekerjaanName,
            'pendapatan_range' => $latestWork ? $latestWork['pendapatan_range'] : null,
            'pendidikan_terakhir' => $highestEdu,
            'keahlian' => !empty($keahlianText) ? $keahlianText : null
        ];

        $this->memberModel->update($memberId, $updateData);
    }

    // =========================================================
    // PROMOTE ANGGOTA MENJADI USER PORTAL
    // =========================================================

    /**
     * Promote satu anggota menjadi user portal (single)
     */
    public function promoteToUser($memberId)
    {
        $member = $this->memberModel->find($memberId);

        if (! $member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan.');
        }

        // Pengecekan: pastikan belum terhubung ke akun di tabel users
        if (! empty($member['user_id'])) {
            $db = \Config\Database::connect();
            $userExists = $db->table('users')->where('id', $member['user_id'])->countAllResults();
            if ($userExists > 0) {
                return redirect()->back()->with('error', 'Anggota ' . $member['nama_lengkap'] . ' sudah memiliki akun portal.');
            }
        }

        // Get email from request POST (if submitted via modal/form) or fallback to database email
        $email = $this->request->getPost('email') ?: $member['email'];
        $email = trim($email);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Email valid wajib diisi untuk membuat user portal.');
        }

        // Update email di database members jika berbeda
        if ($email !== $member['email']) {
            $this->memberModel->update($memberId, ['email' => $email]);
            $member['email'] = $email;
        }

        $result = $this->createUserFromMember($member);

        if ($result['success']) {
            return redirect()->back()->with('success', 'Akun portal berhasil dibuat untuk ' . $member['nama_lengkap'] . ' (Username: ' . $result['username'] . '). Password default: P4nm4s');
        }

        return redirect()->back()->with('error', 'Gagal membuat akun: ' . $result['message']);
    }

    /**
     * Promote massal beberapa anggota sekaligus (bulk via POST checkbox)
     */
    public function bulkPromote()
    {
        $memberIds = $this->request->getPost('member_ids');

        if (empty($memberIds) || ! is_array($memberIds)) {
            return redirect()->back()->with('error', 'Tidak ada anggota yang dipilih.');
        }

        $success = 0;
        $skipped = 0;
        $errors  = [];

        $db = \Config\Database::connect();

        foreach ($memberIds as $memberId) {
            $member = $this->memberModel->find($memberId);
            if (! $member) continue;

            if (! empty($member['user_id'])) {
                $userExists = $db->table('users')->where('id', $member['user_id'])->countAllResults();
                if ($userExists > 0) {
                    $skipped++;
                    continue;
                }
            }

            // Untuk bulk promote, lewati jika anggota tidak memiliki email
            if (empty($member['email']) || !filter_var($member['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = $member['nama_lengkap'] . ': Anggota tidak memiliki email valid.';
                continue;
            }

            $result = $this->createUserFromMember($member);
            if ($result['success']) {
                $success++;
            } else {
                $errors[] = $member['nama_lengkap'] . ': ' . $result['message'];
            }
        }

        $msg = "Berhasil membuat {$success} akun portal";
        if ($skipped > 0) $msg .= ", {$skipped} dilewati (sudah punya akun)";
        if (! empty($errors)) $msg .= ". Lewat/Gagal: " . implode('; ', $errors);

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Helper: Buat Shield user dari data anggota
     */
    private function createUserFromMember(array $member): array
    {
        $db        = \Config\Database::connect();
        $userModel = new \CodeIgniter\Shield\Models\UserModel();

        // --- Generate username dari email ---
        $baseUsername = strtolower(explode('@', $member['email'])[0]);
        $baseUsername = preg_replace('/[^a-z0-9._]/i', '', $baseUsername) ?: 'user';

        // Pastikan username unik
        $username = $baseUsername;
        $counter  = 1;
        while ($db->table('users')->where('username', $username)->countAllResults() > 0) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        $email = $member['email'];

        // Cek email sudah terdaftar
        $emailExists = $db->table('auth_identities')
            ->where('secret', $email)
            ->where('type', 'email_password')
            ->countAllResults();

        if ($emailExists) {
            return ['success' => false, 'message' => 'Email ' . $email . ' sudah terdaftar di sistem.'];
        }

        try {
            // --- Buat user Shield ---
            $newUser = new \CodeIgniter\Shield\Entities\User([
                'username' => $username,
                'active'   => 1,
            ]);
            $newUser->setEmail($email);
            $newUser->setPassword('P4nm4s');

            if (! $userModel->save($newUser)) {
                return ['success' => false, 'message' => implode(', ', $userModel->errors())];
            }

            $userId = $userModel->getInsertID();

            $createdUser = $userModel->findById($userId);
            if ($createdUser) {
                $createdUser->addGroup('user');
            }

            // Tandai harus ganti password
            $db->table('users')->where('id', $userId)->update(['force_password_reset' => 1]);

            // --- Link member ke user ---
            $this->memberModel->update($member['id'], [
                'user_id'        => $userId,
                'account_status' => 'invited',
            ]);

            return ['success' => true, 'user_id' => $userId, 'username' => $username];

        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function dashboard()
    {
        $db = \Config\Database::connect();

        $type        = $this->request->getGet('type') ?: 'all';
        $kelurahanId = $this->request->getGet('kelurahan_id');
        $kelurahanId = ($kelurahanId === null || $kelurahanId === '') ? '' : (string) $kelurahanId;

        $peloporStatuses = ['Madya', 'Dewasa', 'Utama'];

        // Helper: apply jenjang (type) filter to a builder
        $applyType = function ($builder) use ($type, $peloporStatuses) {
            if ($type === 'pelopor') {
                $builder->whereIn('members.status_anggota', $peloporStatuses);
            } elseif ($type === 'non_pelopor') {
                $builder->groupStart()
                    ->whereNotIn('members.status_anggota', $peloporStatuses)
                    ->orWhere('members.status_anggota', null)
                    ->orWhere('members.status_anggota', '')
                    ->groupEnd();
            }
            return $builder;
        };

        // Helper: apply kelurahan filter to a builder ('none' = tanpa kelurahan)
        $applyKel = function ($builder) use ($kelurahanId) {
            if ($kelurahanId === 'none') {
                $builder->groupStart()
                    ->where('members.kelurahan_id IS NULL')
                    ->orWhere('members.kelurahan_id', 0)
                    ->groupEnd();
            } elseif ($kelurahanId !== '') {
                $builder->where('members.kelurahan_id', (int) $kelurahanId);
            }
            return $builder;
        };

        // Fresh builder with both filters applied
        $scoped = function () use ($db, $applyType, $applyKel) {
            return $applyKel($applyType($db->table('members')));
        };

        // Totals for type buttons (respecting kelurahan filter)
        $totalMembersRaw = $applyKel($db->table('members'))->countAllResults();
        $totalPelopor    = $applyKel($db->table('members')->whereIn('members.status_anggota', $peloporStatuses))->countAllResults();
        $totalNonPelopor = $totalMembersRaw - $totalPelopor;

        // KPI counts
        $totalMembers   = $scoped()->countAllResults();
        $activeCount    = $scoped()->where('account_status', 'active')->countAllResults();
        $invitedCount   = $scoped()->where('account_status', 'invited')->countAllResults();
        $noAccountCount = $scoped()->where('account_status', 'no_account')->countAllResults();
        $maleCount      = $scoped()->where('jenis_kelamin', 'L')->countAllResults();
        $femaleCount    = $scoped()->where('jenis_kelamin', 'P')->countAllResults();
        $mentoringCount = $scoped()->where('mengikuti_mentoring', 1)->countAllResults();

        // 10 Profesi Terbanyak (Normalized in PHP)
        $rawProfs = $scoped()->select('pekerjaan')->get()->getResultArray();

        $normalizedProfs = [];
        foreach ($rawProfs as $row) {
            $raw   = trim($row['pekerjaan'] ?? '');
            $lower = strtolower($raw);

            if ($lower === 'irt' || $lower === 'ibu rumah tangga') {
                $norm = 'Ibu Rumah Tangga';
            } elseif ($lower === '0' || $lower === 'o' || $lower === 'belum' || $lower === '-' || $lower === '') {
                $norm = 'Belum';
            } else {
                $norm = ucwords($lower);
            }

            $normalizedProfs[$norm] = ($normalizedProfs[$norm] ?? 0) + 1;
        }

        arsort($normalizedProfs);
        $professions = [];
        foreach (array_slice($normalizedProfs, 0, 10, true) as $name => $c) {
            $professions[] = ['pekerjaan' => $name, 'count' => $c];
        }

        // 10 Pendidikan Terbanyak
        $educations = $scoped()
            ->select('pendidikan_terakhir, COUNT(*) as count')
            ->where('pendidikan_terakhir IS NOT NULL')
            ->where('pendidikan_terakhir !=', '')
            ->where('pendidikan_terakhir !=', '-')
            ->groupBy('pendidikan_terakhir')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // Rentang Pendapatan
        $incomes = $scoped()
            ->select('pendapatan_range, COUNT(*) as count')
            ->where('pendapatan_range IS NOT NULL')
            ->where('pendapatan_range !=', '')
            ->where('pendapatan_range !=', '-')
            ->groupBy('pendapatan_range')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        // Rentang Usia
        $ages = $scoped()
            ->select('usia_range, COUNT(*) as count')
            ->where('usia_range IS NOT NULL')
            ->where('usia_range !=', '')
            ->where('usia_range !=', '-')
            ->groupBy('usia_range')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        // ── Breakdown per Kelurahan (respecting type filter, all kelurahan) ──
        $pel = "'" . implode("','", $peloporStatuses) . "'";
        $kelBuilder = $applyType($db->table('members'))
            ->select("members.kelurahan_id, v.name AS kelurahan,
                COUNT(*) AS total,
                SUM(CASE WHEN members.status_anggota IN ($pel) THEN 1 ELSE 0 END) AS pelopor,
                SUM(CASE WHEN members.jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS laki,
                SUM(CASE WHEN members.jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS perempuan,
                SUM(CASE WHEN members.account_status = 'active' THEN 1 ELSE 0 END) AS aktif,
                SUM(CASE WHEN members.mengikuti_mentoring = 1 THEN 1 ELSE 0 END) AS mentoring,
                SUM(CASE WHEN members.pendapatan_range IS NOT NULL AND members.pendapatan_range NOT IN ('', '-') THEN 1 ELSE 0 END) AS pendapatan_terisi,
                SUM(CASE WHEN members.pendapatan_range IN ('Rp5.000.000 - Rp10.000.000', '> Rp10.000.000') THEN 1 ELSE 0 END) AS pendapatan_atas_5jt", false)
            ->join('cms_region04_villages v', 'v.id = members.kelurahan_id', 'left')
            ->groupBy('members.kelurahan_id, v.name')
            ->orderBy('total', 'DESC');
        $kelurahanBreakdown = $kelBuilder->get()->getResultArray();

        // Dropdown options (all kelurahan that have members)
        $kelurahanOptions = $db->query("
            SELECT v.id, v.name, COUNT(m.id) AS total
            FROM members m
            JOIN cms_region04_villages v ON v.id = m.kelurahan_id
            GROUP BY v.id, v.name
            ORDER BY v.name ASC
        ")->getResultArray();
        $noKelurahanCount = $db->table('members')
            ->groupStart()->where('kelurahan_id IS NULL')->orWhere('kelurahan_id', 0)->groupEnd()
            ->countAllResults();

        $selectedKelurahanName = '';
        if ($kelurahanId === 'none') {
            $selectedKelurahanName = 'Tanpa Kelurahan';
        } elseif ($kelurahanId !== '') {
            foreach ($kelurahanOptions as $opt) {
                if ((string) $opt['id'] === $kelurahanId) {
                    $selectedKelurahanName = ucwords(strtolower($opt['name']));
                    break;
                }
            }
        }

        $data = [
            'title'                 => 'Dashboard Anggota',
            'type'                  => $type,
            'kelurahanId'           => $kelurahanId,
            'selectedKelurahanName' => $selectedKelurahanName,
            'kelurahanOptions'      => $kelurahanOptions,
            'noKelurahanCount'      => $noKelurahanCount,
            'kelurahanBreakdown'    => $kelurahanBreakdown,
            'totalMembersRaw'       => $totalMembersRaw,
            'totalPelopor'          => $totalPelopor,
            'totalNonPelopor'       => $totalNonPelopor,
            'totalMembers'          => $totalMembers,
            'activeCount'           => $activeCount,
            'invitedCount'          => $invitedCount,
            'noAccountCount'        => $noAccountCount,
            'maleCount'             => $maleCount,
            'femaleCount'           => $femaleCount,
            'mentoringCount'        => $mentoringCount,
            'professions'           => $professions,
            'educations'            => $educations,
            'incomes'               => $incomes,
            'ages'                  => $ages
        ];

        return view('admin/members/dashboard', $data);
    }

    // =========================================================
    // DATA EKONOMI & PENDAPATAN ANGGOTA
    // =========================================================

    /**
     * Halaman Data Ekonomi Anggota berdasarkan Pendapatan per Bulan
     */
    public function ekonomi()
    {
        $mode = $this->request->getGet('mode') ?: ($this->request->getVar('mode') ?: 'keluarga');
        if ($mode === 'individu') {
            return $this->renderEkonomiIndividu();
        }
        return $this->renderEkonomiKeluarga();
    }

    private function renderEkonomiIndividu()
    {
        $db = \Config\Database::connect();

        $search          = trim((string)($this->request->getGet('search') ?? $this->request->getVar('search')));
        $pendapatanRange = $this->request->getGet('pendapatan_range') ?? $this->request->getVar('pendapatan_range');
        $statusAnggota   = $this->request->getGet('status_anggota') ?? ($this->request->getVar('status_anggota') ?? 'pelopor');
        $kodeUpa         = $this->request->getGet('kode_upa') ?? $this->request->getVar('kode_upa');
        $kelurahanId     = $this->request->getGet('kelurahan_id') ?? $this->request->getVar('kelurahan_id');
        $pekerjaan       = $this->request->getGet('pekerjaan') ?? $this->request->getVar('pekerjaan');

        // ── 1. KPI STATS (Calculated for current scope: Pelopor / All / etc.) ────
        $statQuery = $db->table('members');
        if ($statusAnggota === 'pelopor') {
            $statQuery->whereIn('status_anggota', ['Madya', 'Dewasa', 'Utama']);
        } elseif ($statusAnggota === 'non_pelopor') {
            $statQuery->groupStart()
                ->whereNotIn('status_anggota', ['Madya', 'Dewasa', 'Utama'])
                ->orWhere('status_anggota', null)
                ->orWhere('status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota !== 'all' && !empty($statusAnggota)) {
            $statQuery->where('status_anggota', $statusAnggota);
        }

        $allScopeMembers = $statQuery->get()->getResultArray();
        $totalScope = count($allScopeMembers);

        $tierUnder1M  = 0;
        $tier1M3M     = 0;
        $tier3M5M     = 0;
        $tier5M10M    = 0;
        $tierAbove10M = 0;
        $tierUnfilled = 0;

        $profStats = [];

        foreach ($allScopeMembers as $m) {
            $rng = trim((string)($m['pendapatan_range'] ?? ''));
            if ($rng === '< Rp1.000.000') {
                $tierUnder1M++;
            } elseif ($rng === 'Rp1.000.000 - Rp3.000.000') {
                $tier1M3M++;
            } elseif ($rng === 'Rp3.000.000 - Rp5.000.000') {
                $tier3M5M++;
            } elseif ($rng === 'Rp5.000.000 - Rp10.000.000') {
                $tier5M10M++;
            } elseif ($rng === '> Rp10.000.000') {
                $tierAbove10M++;
            } else {
                $tierUnfilled++;
            }

            if (!empty($m['pekerjaan'])) {
                $pek = trim($m['pekerjaan']);
                $profStats[$pek] = ($profStats[$pek] ?? 0) + 1;
            }
        }

        arsort($profStats);
        $topProfessions = array_slice($profStats, 0, 8, true);

        $totalFilled   = $totalScope - $tierUnfilled;
        $percentFilled = $totalScope > 0 ? round(($totalFilled / $totalScope) * 100, 1) : 0;

        // ── 2. FILTERED MEMBER LIST FOR TABLE ─────────────────────────────────
        $query = $this->memberModel
            ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan')
            ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left');

        if (!empty($search)) {
            $query->groupStart()
                ->like('members.nama_lengkap', $search)
                ->orLike('members.pekerjaan', $search)
                ->orLike('members.nama_instansi', $search)
                ->orLike('members.bidang_pekerjaan', $search)
                ->orLike('members.keahlian', $search)
                ->groupEnd();
        }

        if ($statusAnggota === 'pelopor') {
            $query->whereIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama']);
        } elseif ($statusAnggota === 'non_pelopor') {
            $query->groupStart()
                ->whereNotIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama'])
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota !== 'all' && !empty($statusAnggota)) {
            $query->where('members.status_anggota', $statusAnggota);
        }

        if ($pendapatanRange === 'belum_mengisi') {
            $query->groupStart()
                ->where('members.pendapatan_range IS NULL')
                ->orWhere('members.pendapatan_range', '')
                ->orWhere('members.pendapatan_range', '-')
                ->groupEnd();
        } elseif (!empty($pendapatanRange)) {
            $query->where('members.pendapatan_range', $pendapatanRange);
        }

        if (!empty($kodeUpa)) {
            $query->where('members.kode_upa', $kodeUpa);
        }

        if (!empty($kelurahanId)) {
            $query->where('members.kelurahan_id', $kelurahanId);
        }

        if (!empty($pekerjaan)) {
            $query->where('members.pekerjaan', $pekerjaan);
        }

        // Custom Ordering: Terisi pendapatan diurutkan dari tinggi ke rendah, lalu nama
        $query->orderBy("
            CASE 
                WHEN members.pendapatan_range = '> Rp10.000.000' THEN 1
                WHEN members.pendapatan_range = 'Rp5.000.000 - Rp10.000.000' THEN 2
                WHEN members.pendapatan_range = 'Rp3.000.000 - Rp5.000.000' THEN 3
                WHEN members.pendapatan_range = 'Rp1.000.000 - Rp3.000.000' THEN 4
                WHEN members.pendapatan_range = '< Rp1.000.000' THEN 5
                ELSE 6
            END ASC, members.nama_lengkap ASC
        ");

        $members = $query->findAll();

        // ── 3. LOOKUPS FOR FILTERS ───────────────────────────────────────────
        $regions = $this->regionModel
            ->select('regions_upa.*, cms_region04_villages.name AS kelurahan')
            ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
            ->orderBy('regions_upa.kode_upa', 'ASC')
            ->findAll();

        $villages = $db->query("
            SELECT DISTINCT v.id, v.name 
            FROM cms_region04_villages v 
            JOIN members m ON m.kelurahan_id = v.id 
            ORDER BY v.name ASC
        ")->getResultArray();

        $jobList = $db->query("
            SELECT DISTINCT pekerjaan 
            FROM members 
            WHERE pekerjaan IS NOT NULL AND pekerjaan != '' AND pekerjaan != '-' 
            ORDER BY pekerjaan ASC
        ")->getResultArray();

        $jobFields = (new JobFieldModel())->orderBy('name', 'ASC')->findAll();

        // Resolve permissions
        $permModel = new \App\Models\RolePermissionModel();
        $canCreate = false;
        $canUpdate = false;
        $canDelete = false;
        $user = auth()->user();
        if ($user) {
            $groups = $user->getGroups();
            foreach ($groups as $grp) {
                $perms = $permModel->getByRole($grp);
                if (!empty($perms['members'])) {
                    if ($perms['members']['can_create']) $canCreate = true;
                    if ($perms['members']['can_update']) $canUpdate = true;
                    if ($perms['members']['can_delete']) $canDelete = true;
                }
            }
            if (in_array('admin', $groups)) {
                $canCreate = $canUpdate = $canDelete = true;
            }
        }

        return view('admin/members/ekonomi', [
            'title'           => 'Data & Kondisi Ekonomi Anggota',
            'mode'            => 'individu',
            'members'         => $members,
            'totalScope'      => $totalScope,
            'totalFilled'     => $totalFilled,
            'totalUnfilled'   => $tierUnfilled,
            'tierUnfilled'    => $tierUnfilled,
            'percentFilled'   => $percentFilled,
            'tierUnder1M'     => $tierUnder1M,
            'tier1M3M'        => $tier1M3M,
            'tier3M5M'        => $tier3M5M,
            'tier5M10M'       => $tier5M10M,
            'tierAbove10M'    => $tierAbove10M,
            'topProfessions'  => $topProfessions,
            'regions'         => $regions,
            'villages'        => $villages,
            'jobList'         => $jobList,
            'jobFields'       => $jobFields,
            'filter'          => [
                'mode'             => 'individu',
                'search'           => $search,
                'pendapatan_range' => $pendapatanRange,
                'status_anggota'   => $statusAnggota,
                'kode_upa'         => $kodeUpa,
                'kelurahan_id'     => $kelurahanId,
                'pekerjaan'        => $pekerjaan,
            ],
            'can_create'      => $canCreate,
            'can_update'      => $canUpdate,
            'can_delete'      => $canDelete,
        ]);
    }

    /**
     * Quick Update Data Ekonomi via Modal / AJAX
     */
    public function quickUpdateEkonomi()
    {
        $memberId = (int)$this->request->getVar('member_id');
        $member   = $this->memberModel->find($memberId);
        if (!$member) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anggota tidak ditemukan.']);
        }

        $pendapatanRange = $this->request->getVar('pendapatan_range');
        $pekerjaan       = trim((string)$this->request->getVar('pekerjaan'));
        $namaInstansi    = trim((string)$this->request->getVar('nama_instansi'));
        $bidangPekerjaan = trim((string)$this->request->getVar('bidang_pekerjaan'));

        $this->memberModel->update($memberId, [
            'pendapatan_range' => $pendapatanRange,
            'pekerjaan'        => $pekerjaan ?: $member['pekerjaan'],
            'nama_instansi'    => $namaInstansi ?: $member['nama_instansi'],
            'bidang_pekerjaan' => $bidangPekerjaan ?: $member['bidang_pekerjaan'],
        ]);

        // Keep member_work_experiences in sync
        $workModel = new \App\Models\WorkExperienceModel();
        $latestWork = $workModel->where('member_id', $memberId)->orderBy('id', 'DESC')->first();
        if ($latestWork) {
            $workModel->update($latestWork['id'], [
                'pendapatan_range' => $pendapatanRange,
                'jabatan'          => $pekerjaan ?: $latestWork['jabatan'],
                'nama_perusahaan'  => $namaInstansi ?: $latestWork['nama_perusahaan'],
            ]);
        } elseif (!empty($pekerjaan) || !empty($pendapatanRange)) {
            $workModel->insert([
                'member_id'        => $memberId,
                'jabatan'          => $pekerjaan ?: '-',
                'nama_perusahaan'  => $namaInstansi ?: '-',
                'pendapatan_range' => $pendapatanRange,
                'rentang_tahun'    => 'Saat Ini',
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data ekonomi ' . $member['nama_lengkap'] . ' berhasil diperbarui!'
        ]);
    }

    /**
     * Export Data Ekonomi Anggota ke Excel (.xlsx)
     */
    public function exportEkonomi()
    {
        $mode = $this->request->getGet('mode') ?: ($this->request->getVar('mode') ?: 'keluarga');
        if ($mode === 'keluarga') {
            return $this->exportEkonomiKeluarga();
        }

        $db = \Config\Database::connect();

        $search          = trim((string)$this->request->getVar('search'));
        $pendapatanRange = $this->request->getVar('pendapatan_range');
        $statusAnggota   = $this->request->getVar('status_anggota') ?? 'pelopor';
        $kodeUpa         = $this->request->getVar('kode_upa');
        $kelurahanId     = $this->request->getVar('kelurahan_id');
        $pekerjaan       = $this->request->getVar('pekerjaan');

        $query = $this->memberModel
            ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan')
            ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left');

        if (!empty($search)) {
            $query->groupStart()
                ->like('members.nama_lengkap', $search)
                ->orLike('members.pekerjaan', $search)
                ->orLike('members.nama_instansi', $search)
                ->orLike('members.bidang_pekerjaan', $search)
                ->orLike('members.keahlian', $search)
                ->groupEnd();
        }

        if ($statusAnggota === 'pelopor') {
            $query->whereIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama']);
        } elseif ($statusAnggota === 'non_pelopor') {
            $query->groupStart()
                ->whereNotIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama'])
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota !== 'all' && !empty($statusAnggota)) {
            $query->where('members.status_anggota', $statusAnggota);
        }

        if ($pendapatanRange === 'belum_mengisi') {
            $query->groupStart()
                ->where('members.pendapatan_range IS NULL')
                ->orWhere('members.pendapatan_range', '')
                ->orWhere('members.pendapatan_range', '-')
                ->groupEnd();
        } elseif (!empty($pendapatanRange)) {
            $query->where('members.pendapatan_range', $pendapatanRange);
        }

        if (!empty($kodeUpa)) $query->where('members.kode_upa', $kodeUpa);
        if (!empty($kelurahanId)) $query->where('members.kelurahan_id', $kelurahanId);
        if (!empty($pekerjaan)) $query->where('members.pekerjaan', $pekerjaan);

        $query->orderBy("
            CASE 
                WHEN members.pendapatan_range = '> Rp10.000.000' THEN 1
                WHEN members.pendapatan_range = 'Rp5.000.000 - Rp10.000.000' THEN 2
                WHEN members.pendapatan_range = 'Rp3.000.000 - Rp5.000.000' THEN 3
                WHEN members.pendapatan_range = 'Rp1.000.000 - Rp3.000.000' THEN 4
                WHEN members.pendapatan_range = '< Rp1.000.000' THEN 5
                ELSE 6
            END ASC, members.nama_lengkap ASC
        ");

        $members = $query->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Ekonomi Anggota');

        // Headers styling
        $sheet->setCellValue('A1', 'DATA EKONOMI & PENDAPATAN ANGGOTA - DPC PKS PANCORAN MAS');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('3E2723'));

        $sheet->setCellValue('A2', 'Diexport pada: ' . date('d F Y H:i') . ' WIB | Total Data: ' . count($members) . ' Anggota');
        $sheet->mergeCells('A2:L2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('666666'));

        $headers = [
            'A4' => 'No',
            'B4' => 'Nama Lengkap',
            'C4' => 'Jenis Kelamin',
            'D4' => 'Jenjang',
            'E4' => 'Kode UPA',
            'F4' => 'Kelurahan',
            'G4' => 'RW / RT',
            'H4' => 'No. Handphone',
            'I4' => 'Pekerjaan / Profesi',
            'J4' => 'Instansi / Perusahaan',
            'K4' => 'Bidang Usaha',
            'L4' => 'Pendapatan per Bulan',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A4:L4')->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A4:L4')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('3E2723');
        $sheet->getStyle('A4:L4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum = 5;
        $no = 1;
        foreach ($members as $m) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $m['nama_lengkap']);
            $sheet->setCellValue('C' . $rowNum, $m['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($m['jenis_kelamin'] === 'P' ? 'Perempuan' : '-'));
            $sheet->setCellValue('D' . $rowNum, $m['status_anggota'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, $m['kode_upa'] ?? '-');
            $sheet->setCellValue('F' . $rowNum, $m['kelurahan'] ?? '-');
            $sheet->setCellValue('G' . $rowNum, 'RW ' . ($m['rw'] ?? '-') . ' / RT ' . ($m['rt'] ?? '-'));
            $sheet->setCellValueExplicit('H' . $rowNum, $m['handphone'] ?? '-', DataType::TYPE_STRING);
            $sheet->setCellValue('I' . $rowNum, $m['pekerjaan'] ?? '-');
            $sheet->setCellValue('J' . $rowNum, $m['nama_instansi'] ?? '-');
            $sheet->setCellValue('K' . $rowNum, $m['bidang_pekerjaan'] ?? '-');
            $sheet->setCellValue('L' . $rowNum, $m['pendapatan_range'] ?: 'Belum Mengisi');

            if ($rowNum % 2 == 0) {
                $sheet->getStyle("A{$rowNum}:L{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('F9F6F0');
            }
            $rowNum++;
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Data_Ekonomi_Anggota_Panmas_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // =========================================================
    // KONDISI EKONOMI PER KEPALA KELUARGA (KK)
    // =========================================================

    /** Estimasi nilai tengah (Rp/bulan) untuk tiap rentang pendapatan */
    private const INCOME_MIDPOINTS = [
        '< Rp1.000.000'              => 500000,
        'Rp1.000.000 - Rp3.000.000'  => 2000000,
        'Rp3.000.000 - Rp5.000.000'  => 4000000,
        'Rp5.000.000 - Rp10.000.000' => 7500000,
        '> Rp10.000.000'             => 12500000,
    ];

    /**
     * Kategori kondisi ekonomi berdasarkan pendapatan per kapita (Rp/orang/bulan).
     * Batas bawah Prasejahtera mengacu kira-kira pada garis kemiskinan Kota Depok (~Rp 800 rb/kapita/bulan).
     */
    private const WELFARE_CATEGORIES = [
        'prasejahtera' => ['label' => 'Prasejahtera', 'min' => 0,       'max' => 800000,  'color' => '#dc2626', 'bg' => '#fef2f2', 'desc' => '< Rp 800 rb / kapita'],
        'rentan'       => ['label' => 'Rentan',       'min' => 800000,  'max' => 1500000, 'color' => '#f97316', 'bg' => '#fff7ed', 'desc' => 'Rp 800 rb – 1,5 jt / kapita'],
        'menengah'     => ['label' => 'Menengah',     'min' => 1500000, 'max' => 3000000, 'color' => '#0284c7', 'bg' => '#f0f9ff', 'desc' => 'Rp 1,5 – 3 jt / kapita'],
        'sejahtera'    => ['label' => 'Sejahtera',    'min' => 3000000, 'max' => null,    'color' => '#059669', 'bg' => '#ecfdf5', 'desc' => '≥ Rp 3 jt / kapita'],
    ];

    /** Kelompok total pendapatan keluarga (Rp/bulan) untuk matriks */
    private const FAMILY_INCOME_TIERS = [
        'lt3'   => ['label' => '< 3 jt',    'min' => 0,        'max' => 3000000],
        '3_5'   => ['label' => '3 – 5 jt',  'min' => 3000000,  'max' => 5000000],
        '5_10'  => ['label' => '5 – 10 jt', 'min' => 5000000,  'max' => 10000000],
        '10_15' => ['label' => '10 – 15 jt','min' => 10000000, 'max' => 15000000],
        'gt15'  => ['label' => '≥ 15 jt',   'min' => 15000000, 'max' => null],
    ];

    private function incomeMid(?string $range): ?int
    {
        $range = trim((string) $range);
        return self::INCOME_MIDPOINTS[$range] ?? null;
    }

    private function classifyWelfare(?float $perCapita): string
    {
        if ($perCapita === null) return 'belum';
        foreach (self::WELFARE_CATEGORIES as $key => $c) {
            if ($perCapita >= $c['min'] && ($c['max'] === null || $perCapita < $c['max'])) return $key;
        }
        return 'belum';
    }

    private function classifyFamilyIncome(?float $total): string
    {
        if ($total === null) return 'belum';
        foreach (self::FAMILY_INCOME_TIERS as $key => $t) {
            if ($total >= $t['min'] && ($t['max'] === null || $total < $t['max'])) return $key;
        }
        return 'belum';
    }

    /**
     * Membentuk unit Kepala Keluarga (KK) dari data anggota:
     * - Pasangan (spouse_member_id) digabung jadi 1 KK, kepala = suami (L).
     * - Anak (father/mother_member_id) yang belum menikah dihitung sebagai tanggungan KK orang tuanya.
     * - Anggota lain yang berdiri sendiri = KK tunggal.
     * - Status "Menikah" tapi pasangan belum terdata → pasangan tetap dihitung 1 jiwa (tanpa pendapatan).
     * Pendapatan KK = pendapatan kepala + pendapatan pasangan (nilai tengah rentang).
     */
    private function buildHouseholds(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->query("
            SELECT m.id, m.nama_lengkap, m.jenis_kelamin, m.status_anggota, m.status_perkawinan,
                   m.spouse_member_id, m.father_member_id, m.mother_member_id,
                   m.pendapatan_range, m.pekerjaan, m.handphone, m.kode_upa, m.kelurahan_id, m.rt, m.rw,
                   v.name AS kelurahan
            FROM members m
            LEFT JOIN cms_region04_villages v ON v.id = m.kelurahan_id
        ")->getResultArray();

        $byId = [];
        foreach ($rows as $r) $byId[(int) $r['id']] = $r;

        $married = ['Menikah', 'Duda/Janda'];

        // 1. Tanggungan (anak belum menikah yang orang tuanya terdata)
        $childrenOf = [];   // parentId => [childIds]
        $isDependent = [];
        foreach ($byId as $id => $r) {
            $fid = (int) ($r['father_member_id'] ?? 0);
            $mid = (int) ($r['mother_member_id'] ?? 0);
            $hasParent = ($fid && isset($byId[$fid])) || ($mid && isset($byId[$mid]));
            if ($hasParent && empty($r['spouse_member_id']) && !in_array($r['status_perkawinan'], $married, true)) {
                $isDependent[$id] = true;
                if ($fid && isset($byId[$fid])) $childrenOf[$fid][] = $id;
                if ($mid && isset($byId[$mid])) $childrenOf[$mid][] = $id;
            }
        }

        // 2. Bentuk KK
        $households = [];
        $assigned   = [];
        foreach ($byId as $id => $r) {
            if (isset($assigned[$id]) || isset($isDependent[$id])) continue;

            $spouseId = (int) ($r['spouse_member_id'] ?? 0);
            $spouse   = ($spouseId && isset($byId[$spouseId]) && !isset($assigned[$spouseId])) ? $byId[$spouseId] : null;

            $head = $r;
            if ($spouse && ($r['jenis_kelamin'] === 'P' && $spouse['jenis_kelamin'] !== 'P')) {
                [$head, $spouse] = [$spouse, $r];
            }
            $headId = (int) $head['id'];
            $assigned[$headId] = true;
            if ($spouse) $assigned[(int) $spouse['id']] = true;

            // Anak dari kepala / pasangan
            $kids = array_merge($childrenOf[$headId] ?? [], $spouse ? ($childrenOf[(int) $spouse['id']] ?? []) : []);
            $kids = array_values(array_unique($kids));
            $childNames = [];
            foreach ($kids as $k) { $assigned[$k] = true; $childNames[] = $byId[$k]['nama_lengkap']; }

            $spouseUnregistered = !$spouse && ($head['status_perkawinan'] === 'Menikah');

            $headInc   = $this->incomeMid($head['pendapatan_range']);
            $spouseInc = $spouse ? $this->incomeMid($spouse['pendapatan_range']) : null;

            $size = 1 + ($spouse || $spouseUnregistered ? 1 : 0) + count($kids);

            if ($headInc === null && $spouseInc === null) {
                $total = null;
                $dataStatus = 'belum';
            } else {
                $total = (int) ($headInc ?? 0) + (int) ($spouseInc ?? 0);
                $dataStatus = ($headInc !== null && (!$spouse || $spouseInc !== null)) ? 'lengkap' : 'sebagian';
            }
            $perCapita = $total !== null ? $total / $size : null;

            $households[] = [
                'head_id'             => $headId,
                'head_name'           => $head['nama_lengkap'],
                'head_gender'         => $head['jenis_kelamin'],
                'head_status'         => $head['status_anggota'],
                'head_job'            => $head['pekerjaan'],
                'head_phone'          => $head['handphone'],
                'head_income_range'   => $head['pendapatan_range'],
                'head_income'         => $headInc,
                'spouse_id'           => $spouse ? (int) $spouse['id'] : null,
                'spouse_name'         => $spouse['nama_lengkap'] ?? null,
                'spouse_status'       => $spouse['status_anggota'] ?? null,
                'spouse_job'          => $spouse['pekerjaan'] ?? null,
                'spouse_income_range' => $spouse['pendapatan_range'] ?? null,
                'spouse_income'       => $spouseInc,
                'spouse_unregistered' => $spouseUnregistered,
                'marital_status'      => $head['status_perkawinan'],
                'children'            => $childNames,
                'children_count'      => count($kids),
                'family_size'         => $size,
                'total_income'        => $total,
                'per_capita'          => $perCapita,
                'data_status'         => $dataStatus,
                'welfare'             => $this->classifyWelfare($perCapita),
                'income_tier'         => $this->classifyFamilyIncome($total),
                'kelurahan_id'        => $head['kelurahan_id'] ?: ($spouse['kelurahan_id'] ?? null),
                'kelurahan'           => $head['kelurahan'] ?: ($spouse['kelurahan'] ?? null),
                'kode_upa'            => $head['kode_upa'] ?: ($spouse['kode_upa'] ?? null),
                'rt'                  => $head['rt'],
                'rw'                  => $head['rw'],
                'statuses'            => array_filter([$head['status_anggota'], $spouse['status_anggota'] ?? null]),
            ];
        }

        return $households;
    }

    /** Terapkan filter halaman KK pada daftar household */
    private function filterHouseholds(array $households, array $f): array
    {
        $pelopor = ['Madya', 'Dewasa', 'Utama'];
        return array_values(array_filter($households, function ($h) use ($f, $pelopor) {
            $st = $f['status_anggota'] ?? 'pelopor';
            if ($st === 'pelopor' && !array_intersect($h['statuses'], $pelopor)) return false;
            if ($st === 'non_pelopor' && array_intersect($h['statuses'], $pelopor)) return false;
            if ($st === 'Belum Tarbiyah') {
                if (!in_array('Belum Tarbiyah', $h['statuses'], true) && !in_array('', $h['statuses'], true) && !in_array(null, $h['statuses'], true)) return false;
            } elseif (!in_array($st, ['pelopor', 'non_pelopor', 'all', ''], true) && !in_array($st, $h['statuses'], true)) {
                return false;
            }

            if (!empty($f['kelurahan_id'])) {
                if ($f['kelurahan_id'] === 'none') { if (!empty($h['kelurahan_id'])) return false; }
                elseif ((string) $h['kelurahan_id'] !== (string) $f['kelurahan_id']) return false;
            }
            if (!empty($f['kode_upa']) && $h['kode_upa'] !== $f['kode_upa']) return false;
            if (!empty($f['welfare']) && $h['welfare'] !== $f['welfare']) return false;
            if (!empty($f['family_size'])) {
                $fs = (int) $f['family_size'];
                if ($fs >= 6 ? $h['family_size'] < 6 : $h['family_size'] !== $fs) return false;
            }
            if (!empty($f['search'])) {
                $hay = strtolower($h['head_name'] . ' ' . $h['spouse_name'] . ' ' . implode(' ', $h['children']));
                if (strpos($hay, strtolower($f['search'])) === false) return false;
            }
            return true;
        }));
    }

    private function householdFilterInput(): array
    {
        return [
            'search'         => trim((string) $this->request->getGet('search')),
            'status_anggota' => $this->request->getGet('status_anggota') ?? 'pelopor',
            'kelurahan_id'   => (string) ($this->request->getGet('kelurahan_id') ?? ''),
            'kode_upa'       => (string) ($this->request->getGet('kode_upa') ?? ''),
            'welfare'        => (string) ($this->request->getGet('welfare') ?? ''),
            'family_size'    => (string) ($this->request->getGet('family_size') ?? ''),
        ];
    }

    private function sortHouseholds(array &$list): void
    {
        $order = ['prasejahtera' => 1, 'rentan' => 2, 'menengah' => 3, 'sejahtera' => 4, 'belum' => 5];
        usort($list, function ($a, $b) use ($order) {
            $c = $order[$a['welfare']] <=> $order[$b['welfare']];
            if ($c !== 0) return $c;
            $c = ($a['per_capita'] ?? PHP_INT_MAX) <=> ($b['per_capita'] ?? PHP_INT_MAX);
            return $c !== 0 ? $c : strcasecmp($a['head_name'], $b['head_name']);
        });
    }

    /**
     * Alias redirect ke halaman terpadu ekonomi mode=keluarga
     */
    public function ekonomiKeluarga()
    {
        return redirect()->to(base_url('admin/members/ekonomi?mode=keluarga'));
    }

    private function renderEkonomiKeluarga()
    {
        $db = \Config\Database::connect();
        $filter = $this->householdFilterInput();
        $filter['mode'] = 'keluarga';

        $all = $this->buildHouseholds();

        // Scope tanpa filter kategori & jumlah keluarga → untuk KPI / grafik / matriks
        $scopeFilter = $filter;
        $scopeFilter['welfare'] = '';
        $scopeFilter['family_size'] = '';
        $scope = $this->filterHouseholds($all, $scopeFilter);

        $list = $this->filterHouseholds($all, $filter);
        $this->sortHouseholds($list);

        // KPI
        $welfareCounts = array_fill_keys(array_merge(array_keys(self::WELFARE_CATEGORIES), ['belum']), 0);
        $sizeKeys = [1, 2, 3, 4, 5, 6];
        $matrix = [];   // [size][incomeTier] => count
        foreach ($sizeKeys as $s) $matrix[$s] = array_fill_keys(array_merge(array_keys(self::FAMILY_INCOME_TIERS), ['belum']), 0);
        $welfareBySize = [];
        foreach ($sizeKeys as $s) $welfareBySize[$s] = array_fill_keys(array_keys($welfareCounts), 0);

        $totalJiwa = 0; $sumIncome = 0; $nIncome = 0; $sumPerCap = 0;
        $couples = 0; $singles = 0;
        foreach ($scope as $h) {
            $welfareCounts[$h['welfare']]++;
            $s = min(6, $h['family_size']);
            $matrix[$s][$h['income_tier']]++;
            $welfareBySize[$s][$h['welfare']]++;
            $totalJiwa += $h['family_size'];
            if ($h['total_income'] !== null) { $sumIncome += $h['total_income']; $sumPerCap += $h['per_capita']; $nIncome++; }
            if ($h['spouse_id']) $couples++; else $singles++;
        }

        $kelurahanOptions = $db->query("
            SELECT DISTINCT v.id, v.name FROM cms_region04_villages v
            JOIN members m ON m.kelurahan_id = v.id ORDER BY v.name ASC
        ")->getResultArray();
        $regions = $this->regionModel
            ->select('regions_upa.*, cms_region04_villages.name AS kelurahan')
            ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
            ->orderBy('regions_upa.kode_upa', 'ASC')
            ->findAll();

        // Resolve permissions
        $permModel = new \App\Models\RolePermissionModel();
        $canCreate = false;
        $canUpdate = false;
        $canDelete = false;
        $user = auth()->user();
        if ($user) {
            $groups = $user->getGroups();
            foreach ($groups as $grp) {
                $perms = $permModel->getByRole($grp);
                if (!empty($perms['members'])) {
                    if ($perms['members']['can_create']) $canCreate = true;
                    if ($perms['members']['can_update']) $canUpdate = true;
                    if ($perms['members']['can_delete']) $canDelete = true;
                }
            }
            if (in_array('admin', $groups)) {
                $canCreate = $canUpdate = $canDelete = true;
            }
        }

        return view('admin/members/ekonomi', [
            'title'              => 'Data & Kondisi Ekonomi Anggota',
            'mode'               => 'keluarga',
            'filter'             => $filter,
            'households'         => $list,
            'totalKK'            => count($scope),
            'totalJiwa'          => $totalJiwa,
            'couples'            => $couples,
            'singles'            => $singles,
            'kkWithIncome'       => $nIncome,
            'avgIncome'          => $nIncome ? $sumIncome / $nIncome : 0,
            'avgPerCapita'       => $nIncome ? $sumPerCap / $nIncome : 0,
            'avgFamilySize'      => count($scope) ? $totalJiwa / count($scope) : 0,
            'welfareCounts'      => $welfareCounts,
            'welfareCategories'  => self::WELFARE_CATEGORIES,
            'familyIncomeTiers'  => self::FAMILY_INCOME_TIERS,
            'incomeMidpoints'    => self::INCOME_MIDPOINTS,
            'matrix'             => $matrix,
            'welfareBySize'      => $welfareBySize,
            'kelurahanOptions'   => $kelurahanOptions,
            'regions'            => $regions,
            'can_create'         => $canCreate,
            'can_update'         => $canUpdate,
            'can_delete'         => $canDelete,
        ]);
    }

    /**
     * Export Kondisi Ekonomi per KK ke Excel
     */
    public function exportEkonomiKeluarga()
    {
        $filter = $this->householdFilterInput();
        $list = $this->filterHouseholds($this->buildHouseholds(), $filter);
        $this->sortHouseholds($list);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ekonomi per KK');

        $sheet->setCellValue('A1', 'KONDISI EKONOMI PER KEPALA KELUARGA - DPC PKS PANCORAN MAS');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('3E2723'));
        $sheet->setCellValue('A2', 'Diexport: ' . date('d F Y H:i') . ' WIB | Total: ' . count($list) . ' KK | Pendapatan = estimasi nilai tengah rentang (kepala + pasangan)');
        $sheet->mergeCells('A2:P2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('666666'));

        $headers = ['No', 'Kepala Keluarga', 'Jenjang', 'Pekerjaan', 'Pendapatan Kepala', 'Pasangan', 'Pekerjaan Pasangan', 'Pendapatan Pasangan',
                    'Jumlah Anak Terdata', 'Jumlah Anggota Keluarga', 'Total Pendapatan KK (Rp)', 'Pendapatan per Kapita (Rp)', 'Kondisi Ekonomi',
                    'Kelengkapan Data', 'Kelurahan', 'UPA'];
        $col = 'A';
        foreach ($headers as $h) { $sheet->setCellValue($col . '4', $h); $col++; }
        $sheet->getStyle('A4:P4')->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A4:P4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('3E2723');
        $sheet->getStyle('A4:P4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);

        $labels = array_map(fn($c) => $c['label'], self::WELFARE_CATEGORIES) + ['belum' => 'Belum Terdata'];
        $dataLabels = ['lengkap' => 'Lengkap', 'sebagian' => 'Sebagian', 'belum' => 'Belum Ada'];

        $r = 5; $no = 1;
        foreach ($list as $h) {
            $spouseLabel = $h['spouse_name'] ?? ($h['spouse_unregistered'] ? '(Pasangan belum terdata)' : '-');
            $vals = [
                $no++, $h['head_name'], $h['head_status'] ?: '-', $h['head_job'] ?: '-', $h['head_income_range'] ?: 'Belum Mengisi',
                $spouseLabel, $h['spouse_job'] ?: '-', $h['spouse_id'] ? ($h['spouse_income_range'] ?: 'Belum Mengisi') : '-',
                $h['children_count'], $h['family_size'], $h['total_income'], $h['per_capita'] !== null ? round($h['per_capita']) : null,
                $labels[$h['welfare']], $dataLabels[$h['data_status']], $h['kelurahan'] ?: '-', $h['kode_upa'] ?: '-',
            ];
            $col = 'A';
            foreach ($vals as $v) { $sheet->setCellValue($col . $r, $v); $col++; }
            $r++;
        }
        $sheet->getStyle('K5:L' . max(5, $r - 1))->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'P') as $c) $sheet->getColumnDimension($c)->setAutoSize(true);

        $filename = 'Ekonomi_per_KK_Panmas_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }

    /**
     * =========================================================================
     * MODUL PENDIDIKAN ANGGOTA
     * =========================================================================
     */
    private function getEducationHierarchy(): array
    {
        return [
            'S3'            => 8,
            'Kuliah S3'     => 8,
            'Doktor'        => 8,
            'S2'            => 7,
            'Kuliah S2'     => 7,
            'Magister'      => 7,
            'S2/S3'         => 7, // fallback legacy
            'S1'            => 5,
            'Kuliah S1'     => 5,
            'D4'            => 5,
            'Kuliah D4'     => 5,
            'D3'            => 4,
            'Kuliah D3'     => 4,
            'D2'            => 3.5,
            'D1'            => 3.5,
            'SMA/SMK'       => 3,
            'SMA'           => 3,
            'SMK'           => 3,
            'MA'            => 3,
            'SMP'           => 2,
            'MTs'           => 2,
            'SD'            => 1,
            'Sekolah Dasar' => 1,
            'MI'            => 1,
            'TK'            => 0.5,
            'Playgroup'     => 0.4,
            'Belum Sekolah' => 0.1,
        ];
    }

    private function normalizeEduCategory(string $val): string
    {
        $val = trim($val);
        if ($val === '') return 'unfilled';
        if (in_array($val, ['S3', 'Kuliah S3', 'Doktor'])) return 's3';
        if (in_array($val, ['S2', 'Kuliah S2', 'Magister', 'S2/S3'])) return 's2';
        if (in_array($val, ['S1', 'Kuliah S1', 'D4', 'Kuliah D4'])) return 's1_d4';
        if (in_array($val, ['D3', 'Kuliah D3', 'D2', 'D1'])) return 'diploma';
        if (in_array($val, ['SMA/SMK', 'SMA', 'SMK', 'MA'])) return 'sma_smk';
        if (in_array($val, ['SMP', 'MTs'])) return 'smp';
        if (in_array($val, ['SD', 'Sekolah Dasar', 'MI'])) return 'sd';
        if (in_array($val, ['TK', 'Playgroup', 'Belum Sekolah'])) return 'dini';
        return 'lainnya';
    }

    private function getEduCategoryInfo(string $cat): array
    {
        switch ($cat) {
            case 's3':
                return ['label' => 'S3 (Doktor)', 'short' => 'S3', 'badge' => 'text-white', 'bg' => '#4a148c'];
            case 's2':
                return ['label' => 'S2 (Magister)', 'short' => 'S2', 'badge' => 'text-white', 'bg' => '#6f42c1'];
            case 's1_d4':
                return ['label' => 'S1 / D4 (Sarjana)', 'short' => 'S1', 'badge' => 'text-white', 'bg' => '#0d6efd'];
            case 'diploma':
                return ['label' => 'D1 – D3 (Diploma)', 'short' => 'D3', 'badge' => 'text-dark', 'bg' => '#0dcaf0'];
            case 'sma_smk':
                return ['label' => 'SMA / SMK / MA', 'short' => 'SMA/SMK', 'badge' => 'text-white', 'bg' => '#198754'];
            case 'smp':
                return ['label' => 'SMP / MTs', 'short' => 'SMP', 'badge' => 'text-dark', 'bg' => '#ffc107'];
            case 'sd':
                return ['label' => 'SD / MI', 'short' => 'SD', 'badge' => 'text-white', 'bg' => '#6c757d'];
            case 'dini':
                return ['label' => 'Pendidikan Dini', 'short' => 'Dini/TK', 'badge' => 'text-muted border', 'bg' => '#e9ecef'];
            default:
                return ['label' => 'Belum Ada Data', 'short' => 'Belum Ada', 'badge' => 'text-danger border', 'bg' => '#fff5f5'];
        }
    }

    private function resolveMemberEducation(array $m, array $edus): array
    {
        $hierarchy = $this->getEducationHierarchy();
        $highestOrder = 0;
        $bestJenjang = '';

        // 1. Cek dari riwayat di tabel member_educations
        foreach ($edus as $e) {
            $j = trim((string)($e['jenjang'] ?? ''));
            $ord = $hierarchy[$j] ?? 0;
            if ($ord > $highestOrder) {
                $highestOrder = $ord;
                $bestJenjang = $j;
            }
        }

        // 2. Cek dari kolom members.pendidikan_terakhir
        $pt = trim((string)($m['pendidikan_terakhir'] ?? ''));
        if ($pt !== '') {
            $ord = $hierarchy[$pt] ?? 0;
            if ($ord >= $highestOrder) {
                $highestOrder = $ord;
                $bestJenjang = $pt;
            }
        }

        // 3. Cek dari kolom members.pendidikan_saat_ini
        $psi = trim((string)($m['pendidikan_saat_ini'] ?? ''));
        if ($psi !== '' && empty($bestJenjang)) {
            $ord = $hierarchy[$psi] ?? 0;
            if ($ord >= $highestOrder) {
                $highestOrder = $ord;
                $bestJenjang = $psi;
            }
        }

        // Normalisasi jika masih ada legacy string 'S2/S3'
        if ($bestJenjang === 'S2/S3') {
            $bestJenjang = 'S2';
        }

        $category = $this->normalizeEduCategory($bestJenjang);
        $catInfo  = $this->getEduCategoryInfo($category);

        // Cari nama sekolah / institusi
        $school = trim((string)($m['sekolah_universitas'] ?? ''));
        if ($school === '' || $school === 'Institusi Pendidikan') {
            foreach ($edus as $e) {
                $inst = trim((string)($e['nama_institusi'] ?? ''));
                if ($inst !== '' && $inst !== 'Institusi Pendidikan') {
                    $school = $inst;
                    break;
                }
            }
        }

        $isRealSchool = ($school !== '' && $school !== 'Institusi Pendidikan');

        return [
            'jenjang_raw'      => $bestJenjang,
            'category'         => $category,
            'cat_label'        => $catInfo['label'],
            'cat_short'        => $catInfo['short'],
            'cat_bg'           => $catInfo['bg'],
            'cat_badge'        => $catInfo['badge'],
            'sekolah'          => $isRealSchool ? $school : '',
            'has_real_school'  => $isRealSchool,
            'jurusan'          => trim((string)($m['jurusan'] ?? '')),
            'educations'       => $edus,
            'history_count'    => count($edus),
        ];
    }

    public function pendidikan()
    {
        $db = \Config\Database::connect();

        $search        = trim((string)($this->request->getGet('search') ?? $this->request->getVar('search')));
        $jenjangFilter = trim((string)($this->request->getGet('jenjang') ?? $this->request->getVar('jenjang')));
        $sekolahFilter = trim((string)($this->request->getGet('sekolah') ?? $this->request->getVar('sekolah')));
        $statusAnggota = $this->request->getGet('status_anggota') ?? ($this->request->getVar('status_anggota') ?? 'pelopor');
        $kodeUpa       = $this->request->getGet('kode_upa') ?? $this->request->getVar('kode_upa');
        $kelurahanId   = $this->request->getGet('kelurahan_id') ?? $this->request->getVar('kelurahan_id');
        $hasSchool     = $this->request->getGet('has_school') ?? $this->request->getVar('has_school');
        $gender        = $this->request->getGet('gender') ?? $this->request->getVar('gender');

        // Fetch all member_educations
        $allEdus = $db->table('member_educations')->orderBy('id', 'ASC')->get()->getResultArray();
        $edusByMember = [];
        foreach ($allEdus as $e) {
            $edusByMember[$e['member_id']][] = $e;
        }

        // Base query for members
        $builder = $this->memberModel
            ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan, regions_upa.nama_group AS nama_group_upa')
            ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left')
            ->join('regions_upa', 'regions_upa.kode_upa = members.kode_upa', 'left');

        // Scope status anggota
        if ($statusAnggota === 'pelopor') {
            $builder->whereIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama']);
        } elseif ($statusAnggota === 'non_pelopor') {
            $builder->groupStart()
                ->whereNotIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama'])
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota === 'Belum Tarbiyah') {
            $builder->groupStart()
                ->where('members.status_anggota', 'Belum Tarbiyah')
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota !== 'all' && !empty($statusAnggota)) {
            $builder->where('members.status_anggota', $statusAnggota);
        }

        if (!empty($gender)) {
            $builder->where('members.jenis_kelamin', $gender);
        }

        if (!empty($kelurahanId)) {
            $builder->where('members.kelurahan_id', $kelurahanId);
        }

        if (!empty($kodeUpa)) {
            $builder->where('members.kode_upa', $kodeUpa);
        }

        $allMembers = $builder->orderBy('members.nama_lengkap', 'ASC')->findAll();

        // Process unified education profile & KPI stats
        $kpi = [
            'total'          => count($allMembers),
            'pt_total'       => 0, // Perguruan Tinggi (S3, S2, S1, Diploma)
            's3'             => 0,
            's2'             => 0,
            's1_d4'          => 0,
            'diploma'        => 0,
            'sma_smk'        => 0,
            'smp'            => 0,
            'sd'             => 0,
            'dini'           => 0,
            'unfilled'       => 0,
            'school_filled'  => 0,
            'school_missing' => 0,
        ];

        $schoolFrequency = [];
        $resolvedMembers = [];

        foreach ($allMembers as $m) {
            $edus = $edusByMember[$m['id']] ?? [];
            $edu = $this->resolveMemberEducation($m, $edus);
            $m['edu'] = $edu;

            // KPI counting for current scope
            $cat = $edu['category'];
            if (isset($kpi[$cat])) {
                $kpi[$cat]++;
            }
            if (in_array($cat, ['s3', 's2', 's1_d4', 'diploma'])) {
                $kpi['pt_total']++;
            }
            if ($edu['has_real_school']) {
                $kpi['school_filled']++;
                $schName = $edu['sekolah'];
                $schoolFrequency[$schName] = ($schoolFrequency[$schName] ?? 0) + 1;
            } else {
                $kpi['school_missing']++;
            }

            // FILTERING IN PHP
            // 1. Filter Search (nama anggota, nama sekolah, jurusan, atau pekerjaan)
            if (!empty($search)) {
                $matchSearch = false;
                $searchLower = mb_strtolower($search);
                if (str_contains(mb_strtolower($m['nama_lengkap']), $searchLower)
                    || str_contains(mb_strtolower($m['pekerjaan'] ?? ''), $searchLower)
                    || str_contains(mb_strtolower($edu['sekolah']), $searchLower)
                    || str_contains(mb_strtolower($edu['jurusan']), $searchLower)) {
                    $matchSearch = true;
                }
                // Also check through edus history
                if (!$matchSearch) {
                    foreach ($edus as $he) {
                        if (str_contains(mb_strtolower($he['nama_institusi'] ?? ''), $searchLower)) {
                            $matchSearch = true;
                            break;
                        }
                    }
                }
                if (!$matchSearch) continue;
            }

            // 2. Filter Jenjang
            if (!empty($jenjangFilter) && $jenjangFilter !== 'all') {
                if ($jenjangFilter === 'pt') {
                    if (!in_array($cat, ['s3', 's2', 's1_d4', 'diploma'])) continue;
                } elseif ($jenjangFilter !== $cat) {
                    continue;
                }
            }

            // 3. Filter Sekolah
            if (!empty($sekolahFilter)) {
                $sekolahLower = mb_strtolower($sekolahFilter);
                $matchSchool = str_contains(mb_strtolower($edu['sekolah']), $sekolahLower);
                if (!$matchSchool) {
                    foreach ($edus as $he) {
                        if (str_contains(mb_strtolower($he['nama_institusi'] ?? ''), $sekolahLower)) {
                            $matchSchool = true;
                            break;
                        }
                    }
                }
                if (!$matchSchool) continue;
            }

            // 4. Filter has_school
            if ($hasSchool === '1' && !$edu['has_real_school']) {
                continue;
            } elseif ($hasSchool === '0' && $edu['has_real_school']) {
                continue;
            }

            $resolvedMembers[] = $m;
        }

        arsort($schoolFrequency);
        $topSchools = array_slice($schoolFrequency, 0, 10, true);

        // Dropdown references
        $villages = $db->table('cms_region04_villages')
            ->where('district_id', '32.76.01')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $upas = $db->table('regions_upa')
            ->orderBy('kode_upa', 'ASC')
            ->get()
            ->getResultArray();

        $perms = $this->getPagePermissions('members');

        $data = [
            'title'           => 'Data Pendidikan & Institusi Anggota',
            'members'         => $resolvedMembers,
            'kpi'             => $kpi,
            'topSchools'      => $topSchools,
            'villages'        => $villages,
            'upas'            => $upas,
            'filter'          => [
                'search'         => $search,
                'jenjang'        => $jenjangFilter,
                'sekolah'        => $sekolahFilter,
                'status_anggota' => $statusAnggota,
                'kelurahan_id'   => $kelurahanId,
                'kode_upa'       => $kodeUpa,
                'has_school'     => $hasSchool,
                'gender'         => $gender,
            ],
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
        ];

        return view('admin/members/pendidikan', $data);
    }

    public function quickUpdatePendidikan()
    {
        $perms = $this->getPagePermissions('members');
        $user = auth()->user();
        if (!($perms['canUpdate'] ?? false) && !($user && ($user->inGroup('admin') || $user->inGroup('kaderisasi')))) {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses untuk mengedit data anggota.']);
        }

        $memberId = (int)$this->request->getPost('member_id');
        $member = $this->memberModel->find($memberId);
        if (!$member) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Anggota tidak ditemukan.']);
        }

        $jenjang = trim((string)$this->request->getPost('jenjang'));
        $namaSekolah = trim((string)$this->request->getPost('sekolah_universitas'));
        $jurusan = trim((string)$this->request->getPost('jurusan'));
        $rentangTahun = trim((string)$this->request->getPost('rentang_tahun'));

        // Update members table
        $updateData = [
            'pendidikan_terakhir' => $jenjang ?: null,
            'sekolah_universitas' => $namaSekolah ?: null,
            'jurusan'             => $jurusan ?: null,
        ];
        $this->memberModel->update($memberId, $updateData);

        // Sync to member_educations
        $db = \Config\Database::connect();
        $existingEdu = $db->table('member_educations')->where('member_id', $memberId)->orderBy('id', 'DESC')->get()->getRowArray();
        if ($existingEdu) {
            $db->table('member_educations')->where('id', $existingEdu['id'])->update([
                'jenjang'        => $jenjang ?: $existingEdu['jenjang'],
                'nama_institusi' => $namaSekolah ?: $existingEdu['nama_institusi'],
                'rentang_tahun'  => $rentangTahun ?: $existingEdu['rentang_tahun'],
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        } else {
            if (!empty($jenjang) || !empty($namaSekolah)) {
                $db->table('member_educations')->insert([
                    'member_id'      => $memberId,
                    'jenjang'        => $jenjang ?: 'Lainnya',
                    'nama_institusi' => $namaSekolah ?: 'Institusi Pendidikan',
                    'rentang_tahun'  => $rentangTahun ?: null,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($this->request->isAJAX() || $this->request->getHeaderLine('accept') == 'application/json' || $this->request->getPost('ajax')) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data pendidikan ' . esc($member['nama_lengkap']) . ' berhasil diperbarui!',
                'data'    => [
                    'member_id'           => $memberId,
                    'jenjang'             => $jenjang,
                    'sekolah_universitas' => $namaSekolah,
                    'jurusan'             => $jurusan,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Data pendidikan ' . esc($member['nama_lengkap']) . ' berhasil diperbarui!');
    }

    public function exportPendidikan()
    {
        $db = \Config\Database::connect();

        $search        = trim((string)($this->request->getGet('search') ?? $this->request->getVar('search')));
        $jenjangFilter = trim((string)($this->request->getGet('jenjang') ?? $this->request->getVar('jenjang')));
        $sekolahFilter = trim((string)($this->request->getGet('sekolah') ?? $this->request->getVar('sekolah')));
        $statusAnggota = $this->request->getGet('status_anggota') ?? ($this->request->getVar('status_anggota') ?? 'pelopor');
        $kodeUpa       = $this->request->getGet('kode_upa') ?? $this->request->getVar('kode_upa');
        $kelurahanId   = $this->request->getGet('kelurahan_id') ?? $this->request->getVar('kelurahan_id');
        $hasSchool     = $this->request->getGet('has_school') ?? $this->request->getVar('has_school');
        $gender        = $this->request->getGet('gender') ?? $this->request->getVar('gender');

        $allEdus = $db->table('member_educations')->orderBy('id', 'ASC')->get()->getResultArray();
        $edusByMember = [];
        foreach ($allEdus as $e) {
            $edusByMember[$e['member_id']][] = $e;
        }

        $builder = $this->memberModel
            ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan, regions_upa.nama_group AS nama_group_upa')
            ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
            ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left')
            ->join('regions_upa', 'regions_upa.kode_upa = members.kode_upa', 'left');

        if ($statusAnggota === 'pelopor') {
            $builder->whereIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama']);
        } elseif ($statusAnggota === 'non_pelopor') {
            $builder->groupStart()
                ->whereNotIn('members.status_anggota', ['Madya', 'Dewasa', 'Utama'])
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota === 'Belum Tarbiyah') {
            $builder->groupStart()
                ->where('members.status_anggota', 'Belum Tarbiyah')
                ->orWhere('members.status_anggota', null)
                ->orWhere('members.status_anggota', '')
                ->groupEnd();
        } elseif ($statusAnggota !== 'all' && !empty($statusAnggota)) {
            $builder->where('members.status_anggota', $statusAnggota);
        }

        if (!empty($gender)) {
            $builder->where('members.jenis_kelamin', $gender);
        }

        if (!empty($kelurahanId)) {
            $builder->where('members.kelurahan_id', $kelurahanId);
        }
        if (!empty($kodeUpa)) {
            $builder->where('members.kode_upa', $kodeUpa);
        }

        $allMembers = $builder->orderBy('members.nama_lengkap', 'ASC')->findAll();
        $filtered = [];

        foreach ($allMembers as $m) {
            $edus = $edusByMember[$m['id']] ?? [];
            $edu = $this->resolveMemberEducation($m, $edus);
            $m['edu'] = $edu;

            $cat = $edu['category'];

            if (!empty($search)) {
                $matchSearch = false;
                $searchLower = mb_strtolower($search);
                if (str_contains(mb_strtolower($m['nama_lengkap']), $searchLower)
                    || str_contains(mb_strtolower($m['pekerjaan'] ?? ''), $searchLower)
                    || str_contains(mb_strtolower($edu['sekolah']), $searchLower)
                    || str_contains(mb_strtolower($edu['jurusan']), $searchLower)) {
                    $matchSearch = true;
                }
                if (!$matchSearch) {
                    foreach ($edus as $he) {
                        if (str_contains(mb_strtolower($he['nama_institusi'] ?? ''), $searchLower)) {
                            $matchSearch = true;
                            break;
                        }
                    }
                }
                if (!$matchSearch) continue;
            }

            if (!empty($jenjangFilter) && $jenjangFilter !== 'all') {
                if ($jenjangFilter === 'pt') {
                    if (!in_array($cat, ['s3', 's2', 's1_d4', 'diploma'])) continue;
                } elseif ($jenjangFilter !== $cat) {
                    continue;
                }
            }

            if (!empty($sekolahFilter)) {
                $sekolahLower = mb_strtolower($sekolahFilter);
                $matchSchool = str_contains(mb_strtolower($edu['sekolah']), $sekolahLower);
                if (!$matchSchool) {
                    foreach ($edus as $he) {
                        if (str_contains(mb_strtolower($he['nama_institusi'] ?? ''), $sekolahLower)) {
                            $matchSchool = true;
                            break;
                        }
                    }
                }
                if (!$matchSchool) continue;
            }

            if ($hasSchool === '1' && !$edu['has_real_school']) {
                continue;
            } elseif ($hasSchool === '0' && $edu['has_real_school']) {
                continue;
            }

            $filtered[] = $m;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pendidikan Anggota');

        // Header Title
        $sheet->setCellValue('A1', 'DATA PENDIDIKAN DAN INSTITUSI ANGGOTA - DPC PKS PANCORAN MAS');
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new Color('3E2723'));
        $sheet->setCellValue('A2', 'Diexport: ' . date('d F Y H:i') . ' WIB | Total: ' . count($filtered) . ' Anggota | Status: ' . ($statusAnggota === 'pelopor' ? 'Pelopor (Madya/Dewasa/Utama)' : ($statusAnggota ?: 'Semua')));
        $sheet->mergeCells('A2:O2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new Color('666666'));

        // Table Column Headers
        $headers = [
            'No',
            'Nama Anggota',
            'L/P',
            'Status Anggota',
            'Kategori Jenjang',
            'Jenjang Terinci',
            'Nama Sekolah / Universitas',
            'Jurusan / Program Studi',
            'Riwayat Pendidikan Tambahan',
            'Pekerjaan',
            'RT / RW',
            'Kelurahan',
            'Group UPA',
            'No. Handphone',
            'Email'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $col++;
        }
        $sheet->getStyle('A4:O4')->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
        $sheet->getStyle('A4:O4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('3E2723');
        $sheet->getStyle('A4:O4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);

        $rowNum = 5;
        $no = 1;
        foreach ($filtered as $m) {
            $edu = $m['edu'];

            $extraHistList = [];
            foreach ($edu['educations'] as $he) {
                $txt = $he['jenjang'] . ': ' . ($he['nama_institusi'] ?: '-');
                if (!empty($he['rentang_tahun'])) {
                    $txt .= ' (' . $he['rentang_tahun'] . ')';
                }
                $extraHistList[] = $txt;
            }
            $extraHistStr = implode("; ", $extraHistList);

            $vals = [
                $no++,
                $m['nama_lengkap'],
                $m['jenis_kelamin'] ?: '-',
                $m['status_anggota'] ?: '-',
                $edu['cat_label'],
                $edu['jenjang_raw'] ?: '-',
                $edu['sekolah'] ?: 'Belum diisi',
                $edu['jurusan'] ?: '-',
                $extraHistStr ?: '-',
                $m['pekerjaan'] ?: '-',
                ($m['rt'] ? 'RT ' . $m['rt'] : '') . ($m['rw'] ? ' / RW ' . $m['rw'] : ''),
                $m['kelurahan'] ?: '-',
                $m['kode_upa'] ?: '-',
                $m['handphone'] ?: '-',
                $m['email'] ?: '-'
            ];

            $col = 'A';
            foreach ($vals as $v) {
                if ($col === 'N' && !empty($v) && $v !== '-') {
                    $sheet->setCellValueExplicit($col . $rowNum, (string)$v, DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValue($col . $rowNum, $v);
                }
                $col++;
            }
            $rowNum++;
        }

        foreach (range('A', 'O') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'Pendidikan_Anggota_Panmas_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        (new Xlsx($spreadsheet))->save('php://output');
        exit;
    }
}

