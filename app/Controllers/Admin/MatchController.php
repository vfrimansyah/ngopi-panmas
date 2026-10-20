<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FamilyMatchCandidateModel;
use App\Models\MemberModel;

class MatchController extends BaseController
{
    public function spouse()
    {
        $candidateModel = new FamilyMatchCandidateModel();
        $memberModel = new MemberModel();

        // Get suggested spouse candidates
        $candidates = $candidateModel
            ->where('relation_type', 'spouse')
            ->whereIn('status', ['suggested_high', 'suggested_low'])
            ->orderBy('score', 'DESC')
            ->findAll();

        foreach ($candidates as &$c) {
            $c['member_a'] = $memberModel
                ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan, cms_region02_cities.name AS kota_kabupaten')
                ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
                ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left')
                ->join('cms_region02_cities', 'cms_region02_cities.id = members.kota_kabupaten_id', 'left')
                ->find($c['member_a_id']);

            $c['member_b'] = $memberModel
                ->select('members.*, cms_region04_villages.name AS kelurahan, cms_region03_districts.name AS kecamatan, cms_region02_cities.name AS kota_kabupaten')
                ->join('cms_region04_villages', 'cms_region04_villages.id = members.kelurahan_id', 'left')
                ->join('cms_region03_districts', 'cms_region03_districts.id = members.kecamatan_id', 'left')
                ->join('cms_region02_cities', 'cms_region02_cities.id = members.kota_kabupaten_id', 'left')
                ->find($c['member_b_id']);
            
            // Get raw spouse name from staging if available
            $db = \Config\Database::connect();
            $stagingA = $db->table('import_staging')->where('created_member_id', $c['member_a_id'])->get()->getRowArray();
            $c['raw_spouse_name'] = $stagingA['col_c'] ?? '';
        }

        return view('admin/match/spouse', [
            'title' => 'Tinjau Pencocokan Pasangan',
            'candidates' => $candidates
        ]);
    }

    public function confirmSpouse($id)
    {
        $candidateModel = new FamilyMatchCandidateModel();
        $memberModel = new MemberModel();

        $candidate = $candidateModel->find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Kandidat tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $memberModel->update($candidate['member_a_id'], [
                'spouse_member_id' => $candidate['member_b_id'],
                'spouse_pairing_status' => 'confirmed'
            ]);

            $memberModel->update($candidate['member_b_id'], [
                'spouse_member_id' => $candidate['member_a_id'],
                'spouse_pairing_status' => 'confirmed'
            ]);

            $candidateModel->update($id, ['status' => 'confirmed']);

            // Reject other candidates for these members
            $candidateModel
                ->where('id !=', $id)
                ->where('relation_type', 'spouse')
                ->groupStart()
                    ->where('member_a_id', $candidate['member_a_id'])
                    ->orWhere('member_b_id', $candidate['member_a_id'])
                    ->orWhere('member_a_id', $candidate['member_b_id'])
                    ->orWhere('member_b_id', $candidate['member_b_id'])
                ->groupEnd()
                ->update(null, ['status' => 'rejected']);

            $db->transCommit();
            return redirect()->back()->with('success', 'Pasangan berhasil dikonfirmasi dan ditautkan.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menautkan pasangan: ' . $e->getMessage());
        }
    }

    public function rejectSpouse($id)
    {
        $candidateModel = new FamilyMatchCandidateModel();
        
        if ($candidateModel->update($id, ['status' => 'rejected'])) {
            return redirect()->back()->with('success', 'Rekomendasi pasangan berhasil ditolak.');
        }

        return redirect()->back()->with('error', 'Gagal menolak rekomendasi.');
    }

    public function child()
    {
        $candidateModel = new FamilyMatchCandidateModel();
        $memberModel = new MemberModel();

        // Get suggested child candidates
        $candidates = $candidateModel
            ->where('relation_type', 'parent_child')
            ->whereIn('status', ['suggested_high', 'suggested_low'])
            ->orderBy('score', 'DESC')
            ->findAll();

        foreach ($candidates as &$c) {
            $c['member_a'] = $memberModel->find($c['member_a_id']);
            $c['member_b'] = $memberModel->find($c['member_b_id']);
            
            // Check if A is shadow and B is shadow (Child A vs Child B)
            if ($c['member_a'] && $c['member_b'] && $c['member_a']['account_status'] === 'no_account' && $c['member_b']['account_status'] === 'no_account') {
                $c['type'] = 'shadow_merge';
                $c['father'] = $memberModel->find($c['member_a']['father_member_id']);
                $c['mother'] = $memberModel->find($c['member_b']['mother_member_id']);
            } else {
                $c['type'] = 'primary_link';
                // Member A is usually child, B is parent
                $c['child'] = $c['member_a'];
                $c['parent'] = $c['member_b'];
            }
        }

        return view('admin/match/child', [
            'title' => 'Tinjau Pencocokan Anak',
            'candidates' => $candidates
        ]);
    }

    public function confirmChild($id)
    {
        $candidateModel = new FamilyMatchCandidateModel();
        $memberModel = new MemberModel();

        $candidate = $candidateModel->find($id);
        if (!$candidate) {
            return redirect()->back()->with('error', 'Kandidat tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $memberA = $memberModel->find($candidate['member_a_id']);
            $memberB = $memberModel->find($candidate['member_b_id']);

            if ($memberA['account_status'] === 'no_account' && $memberB['account_status'] === 'no_account') {
                // Merge shadow profiles: Keep A, enrich from B, delete B
                $mergedData = [
                    'mother_member_id' => $memberA['mother_member_id'] ?: $memberB['mother_member_id'],
                    'father_member_id' => $memberA['father_member_id'] ?: $memberB['father_member_id'],
                ];

                // Merge string values if empty in A but filled in B
                $fieldsToMerge = ['sekolah_universitas', 'jurusan', 'judul_skripsi', 'pekerjaan', 'hobi', 'keahlian', 'status_kerja'];
                foreach ($fieldsToMerge as $f) {
                    if (empty($memberA[$f]) && !empty($memberB[$f])) {
                        $mergedData[$f] = $memberB[$f];
                    }
                }

                $memberModel->update($memberA['id'], $mergedData);
                
                // Set dependents reference on match candidates if any
                $candidateModel->where('member_a_id', $memberB['id'])->update(null, ['member_a_id' => $memberA['id']]);
                $candidateModel->where('member_b_id', $memberB['id'])->update(null, ['member_b_id' => $memberA['id']]);
                
                // Delete shadow profile B
                $memberModel->delete($memberB['id']);
            } else {
                // Primary link: Link child (A) to parent (B)
                $parent = $memberB;
                $updateData = [];
                if ($parent['jenis_kelamin'] === 'L') {
                    $updateData['father_member_id'] = $parent['id'];
                } else {
                    $updateData['mother_member_id'] = $parent['id'];
                }
                
                // If parent has a spouse, link the other parent too
                if ($parent['spouse_member_id'] && $parent['spouse_pairing_status'] === 'confirmed') {
                    $spouse = $memberModel->find($parent['spouse_member_id']);
                    if ($spouse) {
                        if ($spouse['jenis_kelamin'] === 'L') {
                            $updateData['father_member_id'] = $spouse['id'];
                        } else {
                            $updateData['mother_member_id'] = $spouse['id'];
                        }
                    }
                }

                $memberModel->update($memberA['id'], $updateData);
            }

            $candidateModel->update($id, ['status' => 'confirmed']);
            $db->transCommit();
            return redirect()->back()->with('success', 'Data anak berhasil dikonfirmasi.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal konfirmasi data anak: ' . $e->getMessage());
        }
    }

    public function separateChild($id)
    {
        $candidateModel = new FamilyMatchCandidateModel();

        if ($candidateModel->update($id, ['status' => 'rejected'])) {
            return redirect()->back()->with('success', 'Data anak berhasil dipisahkan (diabaikan penggabungannya).');
        }

        return redirect()->back()->with('error', 'Gagal memisahkan data.');
    }
}
