<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberIuranModel extends Model
{
    protected $table            = 'member_iuran_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'member_id',
        'tahun',
        'bulan',
        'status_itb',
        'nominal_itb',
        'status_iwb',
        'nominal_iwb',
        'status_infaq',
        'nominal_infaq',
        'tanggal_bayar',
        'catatan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get monthly payment report for all active members
     */
    public function getMonthlyReport(int $tahun, int $bulan, array $filters = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('members m');

        $builder->select('
            m.id as member_id,
            m.nama_lengkap,
            m.jenis_kelamin,
            m.status_anggota,
            m.kode_upa,
            m.alamat_lengkap,
            p.id as payment_id,
            p.tahun,
            p.bulan,
            COALESCE(p.status_itb, "belum") as status_itb,
            COALESCE(p.nominal_itb, 0) as nominal_itb,
            COALESCE(p.status_iwb, "belum") as status_iwb,
            COALESCE(p.nominal_iwb, 0) as nominal_iwb,
            COALESCE(p.status_infaq, "belum") as status_infaq,
            COALESCE(p.nominal_infaq, 0) as nominal_infaq,
            p.tanggal_bayar,
            p.catatan
        ');

        $builder->join(
            'member_iuran_payments p',
            "p.member_id = m.id AND p.tahun = {$db->escape($tahun)} AND p.bulan = {$db->escape($bulan)}",
            'left'
        );

        $builder->where('m.is_active', 1);
        // Restrict to Anggota Pelopor (Dewasa, Madya, Utama)
        $builder->whereIn('m.status_anggota', ['Dewasa', 'Madya', 'Utama']);

        // Apply search filter
        if (!empty($filters['search'])) {
            $builder->like('m.nama_lengkap', $filters['search']);
        }

        // Apply UPA filter
        if (!empty($filters['kode_upa'])) {
            $builder->where('m.kode_upa', $filters['kode_upa']);
        }

        // Apply Jenjang/Status Anggota filter
        if (!empty($filters['status_anggota'])) {
            $builder->where('m.status_anggota', $filters['status_anggota']);
        }

        // Apply Payment Status filter
        if (!empty($filters['status_pembayaran'])) {
            if ($filters['status_pembayaran'] === 'lunas_itb') {
                $builder->where('p.status_itb', 'lunas');
            } elseif ($filters['status_pembayaran'] === 'belum_itb') {
                $builder->groupStart()
                        ->where('p.status_itb', 'belum')
                        ->orWhere('p.status_itb IS NULL')
                        ->groupEnd();
            } elseif ($filters['status_pembayaran'] === 'lunas_iwb') {
                $builder->where('p.status_iwb', 'lunas');
            } elseif ($filters['status_pembayaran'] === 'belum_iwb') {
                $builder->groupStart()
                        ->where('p.status_iwb', 'belum')
                        ->orWhere('p.status_iwb IS NULL')
                        ->groupEnd();
            } elseif ($filters['status_pembayaran'] === 'lunas_semua') {
                $builder->where('p.status_itb', 'lunas')
                        ->where('p.status_iwb', 'lunas');
            }
        }

        $builder->orderBy('m.nama_lengkap', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get summary metrics for cards
     */
    public function getSummary(int $tahun, int $bulan, array $filters = []): array
    {
        $records = $this->getMonthlyReport($tahun, $bulan, $filters);

        $totalAnggota      = count($records);
        $lunasItbCount     = 0;
        $totalItbAmount    = 0.0;
        $lunasIwbCount     = 0;
        $totalIwbAmount    = 0.0;
        $lunasInfaqCount   = 0;
        $totalInfaqAmount  = 0.0;
        $belumBayarCount   = 0;

        foreach ($records as $r) {
            $itbLunas   = ($r['status_itb'] === 'lunas');
            $iwbLunas   = ($r['status_iwb'] === 'lunas');
            $infaqLunas = ($r['status_infaq'] === 'lunas');

            if ($itbLunas) {
                $lunasItbCount++;
                $totalItbAmount += (float)$r['nominal_itb'];
            }
            if ($iwbLunas) {
                $lunasIwbCount++;
                $totalIwbAmount += (float)$r['nominal_iwb'];
            }
            if ($infaqLunas || (float)$r['nominal_infaq'] > 0) {
                $lunasInfaqCount++;
                $totalInfaqAmount += (float)$r['nominal_infaq'];
            }

            if (!$itbLunas && !$iwbLunas && (float)$r['nominal_infaq'] == 0) {
                $belumBayarCount++;
            }
        }

        return [
            'total_anggota'      => $totalAnggota,
            'lunas_itb_count'    => $lunasItbCount,
            'total_itb_amount'   => $totalItbAmount,
            'lunas_iwb_count'    => $lunasIwbCount,
            'total_iwb_amount'   => $totalIwbAmount,
            'lunas_infaq_count'  => $lunasInfaqCount,
            'total_infaq_amount' => $totalInfaqAmount,
            'belum_bayar_count'  => $belumBayarCount,
        ];
    }

    /**
     * Save or update payment status for a specific member, year, month
     */
    public function saveOrUpdatePayment(int $memberId, int $tahun, int $bulan, array $data): bool
    {
        $existing = $this->where('member_id', $memberId)
                         ->where('tahun', $tahun)
                         ->where('bulan', $bulan)
                         ->first();

        $payload = [
            'member_id'     => $memberId,
            'tahun'         => $tahun,
            'bulan'         => $bulan,
            'status_itb'    => $data['status_itb'] ?? 'belum',
            'nominal_itb'   => (float)($data['nominal_itb'] ?? 0),
            'status_iwb'    => $data['status_iwb'] ?? 'belum',
            'nominal_iwb'   => (float)($data['nominal_iwb'] ?? 0),
            'status_infaq'  => $data['status_infaq'] ?? 'belum',
            'nominal_infaq' => (float)($data['nominal_infaq'] ?? 0),
            'tanggal_bayar' => !empty($data['tanggal_bayar']) ? $data['tanggal_bayar'] : date('Y-m-d'),
            'catatan'       => $data['catatan'] ?? null,
        ];

        if ($existing) {
            return (bool) $this->update($existing['id'], $payload);
        } else {
            return (bool) $this->insert($payload);
        }
    }

    /**
     * Get 12-month payment records for a specific member and year
     */
    public function getAnnualReportForMember(int $memberId, int $tahun): array
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $payments = $this->where('member_id', $memberId)
                         ->where('tahun', $tahun)
                         ->findAll();

        $indexed = [];
        foreach ($payments as $p) {
            $indexed[(int)$p['bulan']] = $p;
        }

        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $payment = $indexed[$m] ?? null;
            $result[$m] = [
                'bulan'         => $m,
                'nama_bulan'    => $bulanNames[$m],
                'status_itb'    => $payment['status_itb'] ?? 'belum',
                'nominal_itb'   => (float)($payment['nominal_itb'] ?? 0),
                'status_iwb'    => $payment['status_iwb'] ?? 'belum',
                'nominal_iwb'   => (float)($payment['nominal_iwb'] ?? 0),
                'status_infaq'  => $payment['status_infaq'] ?? 'belum',
                'nominal_infaq' => (float)($payment['nominal_infaq'] ?? 0),
                'tanggal_bayar' => $payment['tanggal_bayar'] ?? null,
                'catatan'       => $payment['catatan'] ?? null,
            ];
        }

        return $result;
    }

    /**
     * Get summary metrics for a single member's annual iuran
     */
    public function getMemberAnnualSummary(int $memberId, int $tahun): array
    {
        $records = $this->getAnnualReportForMember($memberId, $tahun);

        $totalItb = 0.0;
        $totalIwb = 0.0;
        $totalInfaq = 0.0;
        $lunasItbMonths = 0;
        $lunasIwbMonths = 0;
        $lunasAllMonths = 0;

        foreach ($records as $r) {
            $totalItb += $r['nominal_itb'];
            $totalIwb += $r['nominal_iwb'];
            $totalInfaq += $r['nominal_infaq'];

            $itbOk = ($r['status_itb'] === 'lunas');
            $iwbOk = ($r['status_iwb'] === 'lunas');

            if ($itbOk) $lunasItbMonths++;
            if ($iwbOk) $lunasIwbMonths++;
            if ($itbOk && $iwbOk) $lunasAllMonths++;
        }

        return [
            'total_itb'             => $totalItb,
            'total_iwb'             => $totalIwb,
            'total_infaq'           => $totalInfaq,
            'total_setor'           => $totalItb + $totalIwb + $totalInfaq,
            'lunas_itb_months'      => $lunasItbMonths,
            'lunas_iwb_months'      => $lunasIwbMonths,
            'lunas_all_months'      => $lunasAllMonths,
            'percentage_completed'  => round(($lunasAllMonths / 12) * 100, 1),
        ];
    }
}
