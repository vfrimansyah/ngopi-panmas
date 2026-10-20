<?php
namespace App\Models;
use CodeIgniter\Model;

class FinRekeningModel extends Model
{
    protected $table         = 'fin_rekening';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nama_rekening','nomor_rekening','bank','tipe','akun_id','saldo_awal','is_active'];
    protected $useTimestamps = true;

    /** Rekening aktif dengan info akun */
    public function getWithAkun(): array
    {
        return $this->db->table('fin_rekening r')
            ->select('r.*, a.kode_akun, a.nama_akun')
            ->join('fin_accounts a', 'a.id = r.akun_id', 'left')
            ->where('r.is_active', 1)
            ->orderBy('r.tipe')->orderBy('r.nama_rekening')
            ->get()->getResultArray();
    }

    /**
     * Hitung saldo berjalan: saldo_awal + pemasukan - pengeluaran
     * untuk filter tanggal tertentu
     */
    public function getSaldoBerjalan(int $rekeningId, ?string $sampaiTanggal = null): float
    {
        $db    = \Config\Database::connect();
        $query = $db->table('fin_transaksi')
            ->selectSum('jumlah', 'total')
            ->where('rekening_id', $rekeningId)
            ->where('status', 'posted')
            ->where('tipe', 'pemasukan');
        if ($sampaiTanggal) $query->where('tanggal <=', $sampaiTanggal);
        $masuk = (float) ($query->get()->getRowArray()['total'] ?? 0);

        $query2 = $db->table('fin_transaksi')
            ->selectSum('jumlah', 'total')
            ->where('rekening_id', $rekeningId)
            ->where('status', 'posted')
            ->where('tipe', 'pengeluaran');
        if ($sampaiTanggal) $query2->where('tanggal <=', $sampaiTanggal);
        $keluar = (float) ($query2->get()->getRowArray()['total'] ?? 0);

        $rek = $this->find($rekeningId);
        $saldoAwal = $rek ? (float) $rek['saldo_awal'] : 0;

        return $saldoAwal + $masuk - $keluar;
    }

    /** Total saldo semua rekening */
    public function getTotalSaldo(): float
    {
        $all = $this->where('is_active', 1)->findAll();
        $total = 0;
        foreach ($all as $r) {
            $total += $this->getSaldoBerjalan($r['id']);
        }
        return $total;
    }
}
