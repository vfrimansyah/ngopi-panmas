<?php

namespace App\Models;

use CodeIgniter\Model;

class UpaMentorHistoryModel extends Model
{
    protected $table            = 'upa_mentor_histories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'kode_upa',
        'pembimbing_id',
        'pembimbing_nama',
        'tahun_mulai',
        'tahun_selesai',
        'periode',
        'is_current',
        'catatan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil riwayat para pembimbing untuk suatu UPA
     */
    public function getMentorHistories($kodeUpa)
    {
        return $this->select('upa_mentor_histories.*, members.status_anggota as jenjang_pembimbing, members.email, members.handphone')
            ->join('members', 'members.id = upa_mentor_histories.pembimbing_id', 'left')
            ->where('upa_mentor_histories.kode_upa', $kodeUpa)
            ->orderBy('upa_mentor_histories.is_current', 'DESC')
            ->orderBy('upa_mentor_histories.tahun_mulai', 'DESC')
            ->orderBy('upa_mentor_histories.id', 'DESC')
            ->findAll();
    }

    /**
     * Update pembimbing UPA dan simpan riwayat pergantiannya
     */
    public function updateMentor($kodeUpa, $newMentorId, array $options = [])
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $memberModel = new MemberModel();
            $upaModel = new RegionUpaModel();

            $currentUpa = $upaModel->find($kodeUpa);
            if (!$currentUpa) {
                throw new \Exception("UPA {$kodeUpa} tidak ditemukan.");
            }

            // Jika pembimbing sama, tidak perlu update riwayat
            if ($currentUpa['pembimbing_id'] == $newMentorId) {
                $db->transCommit();
                return true;
            }

            $tahunPergantian = $options['tahun'] ?? date('Y');
            $catatan = $options['catatan'] ?? 'Pergantian pembimbing / naqib';

            // Nonaktifkan pembimbing lama di history
            $activeHistory = $this->where('kode_upa', $kodeUpa)->where('is_current', 1)->first();
            if ($activeHistory) {
                $thnMulai = $activeHistory['tahun_mulai'] ?: $tahunPergantian;
                $this->update($activeHistory['id'], [
                    'is_current'    => 0,
                    'tahun_selesai' => $tahunPergantian,
                    'periode'       => "{$thnMulai} - {$tahunPergantian}",
                    'catatan'       => ($activeHistory['catatan'] ? $activeHistory['catatan'] . ' | ' : '') . "Selesai membimbing pada tahun {$tahunPergantian}",
                ]);
            }

            $newMentorNama = null;
            if (!empty($newMentorId)) {
                $mentor = $memberModel->find($newMentorId);
                $newMentorNama = $mentor ? $mentor['nama_lengkap'] : null;

                // Masukkan pembimbing baru
                $this->insert([
                    'kode_upa'        => $kodeUpa,
                    'pembimbing_id'   => $newMentorId,
                    'pembimbing_nama' => $newMentorNama,
                    'tahun_mulai'     => $tahunPergantian,
                    'tahun_selesai'   => null,
                    'periode'         => "{$tahunPergantian} - Sekarang",
                    'is_current'      => 1,
                    'catatan'         => $catatan,
                ]);
            }

            // Update regions_upa
            $upaModel->update($kodeUpa, [
                'pembimbing_id'   => $newMentorId ?: null,
                'pembimbing_nama' => $newMentorNama,
            ]);

            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
