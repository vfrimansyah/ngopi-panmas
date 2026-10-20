<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberUpaHistoryModel extends Model
{
    protected $table            = 'member_upa_histories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'member_id',
        'kode_upa',
        'nama_group',
        'pembimbing_id',
        'pembimbing_nama',
        'tahun_mulai',
        'tahun_selesai',
        'periode',
        'status',
        'is_current',
        'catatan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Ambil seluruh riwayat pembinaan UPA untuk seorang anggota
     */
    public function getMemberHistories($memberId)
    {
        return $this->select('member_upa_histories.*, regions_upa.kelurahan_id, cms_region04_villages.name as nama_kelurahan, pembimbing.status_anggota as jenjang_pembimbing')
            ->join('regions_upa', 'regions_upa.kode_upa = member_upa_histories.kode_upa', 'left')
            ->join('cms_region04_villages', 'cms_region04_villages.id = regions_upa.kelurahan_id', 'left')
            ->join('members as pembimbing', 'pembimbing.id = member_upa_histories.pembimbing_id', 'left')
            ->where('member_upa_histories.member_id', $memberId)
            ->orderBy('member_upa_histories.is_current', 'DESC')
            ->orderBy('member_upa_histories.tahun_mulai', 'DESC')
            ->orderBy('member_upa_histories.id', 'DESC')
            ->findAll();
    }

    /**
     * Ambil riwayat alumni / anggota lampau yang pernah dibina di UPA tertentu
     */
    public function getUpaAlumni($kodeUpa)
    {
        return $this->select('member_upa_histories.*, members.nama_lengkap, members.jenis_kelamin, members.status_anggota, members.email, members.handphone')
            ->join('members', 'members.id = member_upa_histories.member_id', 'inner')
            ->where('member_upa_histories.kode_upa', $kodeUpa)
            ->where('member_upa_histories.is_current', 0)
            ->orderBy('member_upa_histories.tahun_selesai', 'DESC')
            ->orderBy('member_upa_histories.id', 'DESC')
            ->findAll();
    }

    /**
     * Memasukkan atau memindahkan anggota ke kelompok UPA baru dengan mencatat riwayat
     */
    public function assignMember($memberId, $kodeUpa, array $options = [])
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Ambil data UPA tujuan
            $upaModel = new RegionUpaModel();
            $targetUpa = $upaModel->find($kodeUpa);
            if (!$targetUpa) {
                throw new \Exception("Kelompok UPA {$kodeUpa} tidak ditemukan.");
            }

            // Ambil nama pembimbing jika ada
            $pembimbingNama = $targetUpa['pembimbing_nama'] ?? null;
            if (empty($pembimbingNama) && !empty($targetUpa['pembimbing_id'])) {
                $p = $db->table('members')->select('nama_lengkap')->where('id', $targetUpa['pembimbing_id'])->get()->getRowArray();
                if ($p) {
                    $pembimbingNama = $p['nama_lengkap'];
                }
            }

            $namaGroup = $targetUpa['nama_group'] ?: ('Group ' . $kodeUpa);
            $tahunMulai = $options['tahun_mulai'] ?? date('Y');
            $catatan = $options['catatan'] ?? 'Penempatan ke kelompok UPA';

            // Tutup riwayat aktif sebelumnya jika ada
            $activeHistory = $this->where('member_id', $memberId)->where('is_current', 1)->first();
            if ($activeHistory) {
                // Update history lama menjadi pindah
                $thnSelesai = $tahunMulai;
                $periodeLama = ($activeHistory['tahun_mulai'] ? $activeHistory['tahun_mulai'] : $thnSelesai) . ' - ' . $thnSelesai;
                
                $this->update($activeHistory['id'], [
                    'is_current'    => 0,
                    'status'        => $options['previous_status'] ?? 'pindah',
                    'tahun_selesai' => $thnSelesai,
                    'periode'       => $periodeLama,
                    'catatan'       => ($activeHistory['catatan'] ? $activeHistory['catatan'] . ' | ' : '') . "Dipindahkan ke {$kodeUpa} pada tahun {$thnSelesai}",
                ]);
            }

            // Buat riwayat baru aktif
            $this->insert([
                'member_id'       => $memberId,
                'kode_upa'        => $kodeUpa,
                'nama_group'      => $namaGroup,
                'pembimbing_id'   => $targetUpa['pembimbing_id'] ?: null,
                'pembimbing_nama' => $pembimbingNama,
                'tahun_mulai'     => $tahunMulai,
                'tahun_selesai'   => null,
                'periode'         => "{$tahunMulai} - Sekarang",
                'status'          => 'aktif',
                'is_current'      => 1,
                'catatan'         => $catatan,
            ]);

            // Update kode_upa di tabel members
            $db->table('members')->where('id', $memberId)->update([
                'kode_upa' => $kodeUpa
            ]);

            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Mengeluarkan anggota dari kelompok UPA dengan mencatat riwayat alumni/keluar
     */
    public function removeMember($memberId, $kodeUpa, array $options = [])
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $tahunSelesai = $options['tahun_selesai'] ?? date('Y');
            $status = $options['status'] ?? 'alumni';
            $catatan = $options['catatan'] ?? 'Dikeluarkan / Selesai dari kelompok UPA';

            // Cari histori aktif
            $activeHistory = $this->where('member_id', $memberId)
                ->where('kode_upa', $kodeUpa)
                ->where('is_current', 1)
                ->first();

            if ($activeHistory) {
                $thnMulai = $activeHistory['tahun_mulai'] ?: $tahunSelesai;
                $periode = "{$thnMulai} - {$tahunSelesai}";
                $this->update($activeHistory['id'], [
                    'is_current'    => 0,
                    'status'        => $status,
                    'tahun_selesai' => $tahunSelesai,
                    'periode'       => $periode,
                    'catatan'       => ($activeHistory['catatan'] ? $activeHistory['catatan'] . ' | ' : '') . $catatan,
                ]);
            }

            // Set null kode_upa di members
            $db->table('members')->where('id', $memberId)->update([
                'kode_upa' => null
            ]);

            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
