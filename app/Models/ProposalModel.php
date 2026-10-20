<?php

namespace App\Models;

use CodeIgniter\Model;

class ProposalModel extends Model
{
    protected $table            = 'proposals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lembaga_id',
        'user_id',
        'member_id',
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'judul_proposal',
        'target_pengajuan',
        'tujuan_nama',
        'tujuan_instansi',
        'tujuan_alamat',
        'latar_belakang',
        'maksud_tujuan',
        'deskripsi_kegiatan',
        'sasaran_penerima',
        'waktu_pelaksanaan',
        'lokasi_kegiatan',
        'susunan_panitia',
        'penutup',
        'total_anggaran',
        'terbilang_anggaran',
        'selected_attachments',
        'foto_dokumentasi',
        'status',
        'catatan_admin',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get list of proposals with joined lembaga info
     */
    public function getList(?int $userId = null, ?int $memberId = null, bool $isAdmin = false, array $filters = [])
    {
        $builder = $this->builder();
        $builder->select('proposals.*, lp.nama_lembaga, lp.jenis_lembaga, lp.file_logo, lp.nama_ketua, lp.hp_ketua, m.nama_lengkap as pembuat_nama')
            ->join('lembaga_profiles lp', 'lp.id = proposals.lembaga_id', 'left')
            ->join('members m', 'm.id = proposals.member_id', 'left');

        if (!$isAdmin) {
            $builder->groupStart();
            if ($userId) {
                $builder->where('proposals.user_id', $userId);
            }
            if ($memberId) {
                $builder->orWhere('proposals.member_id', $memberId);
            }
            // Pemilik yayasan juga dapat melihat proposal yang diajukan oleh pengurus di bawah yayasannya
            if ($userId) {
                $builder->orWhere('lp.user_id', $userId);
            }
            if ($memberId) {
                $builder->orWhere('lp.member_id', $memberId);
            }
            $builder->groupEnd();
        }

        if (!empty($filters['target_pengajuan'])) {
            $builder->where('proposals.target_pengajuan', $filters['target_pengajuan']);
        }
        if (!empty($filters['status'])) {
            $builder->where('proposals.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('proposals.judul_proposal', $s)
                ->orLike('proposals.nomor_surat', $s)
                ->orLike('proposals.tujuan_nama', $s)
                ->orLike('proposals.tujuan_instansi', $s)
                ->orLike('lp.nama_lembaga', $s)
                ->groupEnd();
        }

        return $builder->orderBy('proposals.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Get single proposal with joined lembaga
     */
    public function getProposalWithLembaga(int $id)
    {
        return $this->builder()
            ->select('proposals.*, lp.nama_lembaga, lp.jenis_lembaga, lp.kategori_bidang, lp.alamat_lengkap as alamat_lembaga, lp.kelurahan as kelurahan_lembaga, lp.kecamatan as kecamatan_lembaga, lp.kota as kota_lembaga, lp.kode_pos as kodepos_lembaga, lp.no_kontak as telp_lembaga, lp.email as email_lembaga, lp.website as web_lembaga, lp.nama_ketua, lp.nik_ketua, lp.hp_ketua, lp.npwp_ketua, lp.nama_sekretaris, lp.nama_bendahara, lp.no_akta_notaris, lp.nama_notaris, lp.tgl_akta, lp.no_sk_kemenkumham, lp.npwp_lembaga, lp.nib, lp.nama_bank, lp.no_rekening, lp.atas_nama_rekening, lp.file_logo, lp.file_ktp_ketua, lp.file_npwp_ketua, lp.file_npwp_lembaga, lp.file_akta, lp.file_sk_kemenkumham, lp.file_nib, lp.file_buku_rekening, lp.file_domisili, lp.file_susunan_pengurus, m.nama_lengkap as pembuat_nama, m.kode_upa as pembuat_upa')
            ->join('lembaga_profiles lp', 'lp.id = proposals.lembaga_id', 'left')
            ->join('members m', 'm.id = proposals.member_id', 'left')
            ->where('proposals.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Helper: Convert number to Indonesian words (Terbilang)
     */
    public static function terbilang(float|int $nilai): string
    {
        $nilai = abs($nilai);
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $temp  = "";

        if ($nilai < 12) {
            $temp = " " . $huruf[(int)$nilai];
        } else if ($nilai < 20) {
            $temp = self::terbilang($nilai - 10) . " Belas";
        } else if ($nilai < 100) {
            $temp = self::terbilang((int)($nilai / 10)) . " Puluh " . self::terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus " . self::terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = self::terbilang((int)($nilai / 100)) . " Ratus " . self::terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu " . self::terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = self::terbilang((int)($nilai / 1000)) . " Ribu " . self::terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = self::terbilang((int)($nilai / 1000000)) . " Juta " . self::terbilang($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = self::terbilang((int)($nilai / 1000000000)) . " Milyar " . self::terbilang(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = self::terbilang((int)($nilai / 1000000000000)) . " Triliun " . self::terbilang(fmod($nilai, 1000000000000));
        }

        return preg_replace('/\s+/', ' ', trim($temp));
    }
}
