<?php
namespace App\Models;
use CodeIgniter\Model;

class FinTransaksiModel extends Model
{
    protected $table         = 'fin_transaksi';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nomor_transaksi','tanggal','tipe','kategori','deskripsi',
        'jumlah','rekening_id','akun_debit_id','akun_kredit_id',
        'bukti_file','created_by','status',
    ];
    protected $useTimestamps = true;

    /** Generate nomor transaksi otomatis TRX-YYYYMM-XXXX */
    public function generateNomor(): string
    {
        $prefix = 'TRX-' . date('Ym') . '-';
        $last   = $this->like('nomor_transaksi', $prefix, 'after')
            ->orderBy('id', 'DESC')->first();
        $seq = 1;
        if ($last) {
            $parts = explode('-', $last['nomor_transaksi']);
            $seq   = (int) end($parts) + 1;
        }
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /** Daftar transaksi dengan join rekening dan akun */
    public function getList(array $filter = []): array
    {
        $q = $this->db->table('fin_transaksi t')
            ->select('t.*, r.nama_rekening, ad.nama_akun AS nama_akun_debit, ak.nama_akun AS nama_akun_kredit')
            ->join('fin_rekening r',  'r.id = t.rekening_id',    'left')
            ->join('fin_accounts ad', 'ad.id = t.akun_debit_id',  'left')
            ->join('fin_accounts ak', 'ak.id = t.akun_kredit_id', 'left');

        if (!empty($filter['tipe']))        $q->where('t.tipe', $filter['tipe']);
        if (!empty($filter['dari']))        $q->where('t.tanggal >=', $filter['dari']);
        if (!empty($filter['sampai']))      $q->where('t.tanggal <=', $filter['sampai']);
        if (!empty($filter['rekening_id'])) $q->where('t.rekening_id', $filter['rekening_id']);
        if (!empty($filter['status']))      $q->where('t.status', $filter['status']);
        if (!empty($filter['bulan']))       $q->where('MONTH(t.tanggal)', $filter['bulan']);
        if (!empty($filter['tahun']))       $q->where('YEAR(t.tanggal)',  $filter['tahun']);

        return $q->orderBy('t.tanggal', 'DESC')->orderBy('t.id', 'DESC')->get()->getResultArray();
    }

    /** Detail satu transaksi dengan join */
    public function getDetail(int $id): ?array
    {
        return $this->db->table('fin_transaksi t')
            ->select('t.*, r.nama_rekening, r.tipe AS tipe_rekening, ad.kode_akun AS kode_debit, ad.nama_akun AS nama_akun_debit, ak.kode_akun AS kode_kredit, ak.nama_akun AS nama_akun_kredit')
            ->join('fin_rekening r',  'r.id = t.rekening_id',    'left')
            ->join('fin_accounts ad', 'ad.id = t.akun_debit_id',  'left')
            ->join('fin_accounts ak', 'ak.id = t.akun_kredit_id', 'left')
            ->where('t.id', $id)
            ->get()->getRowArray() ?: null;
    }

    /** Total pemasukan periode */
    public function getTotalPemasukan(?int $bulan = null, ?int $tahun = null): float
    {
        $q = $this->where('tipe', 'pemasukan')->where('status', 'posted');
        if ($bulan) $q->where('MONTH(tanggal)', $bulan);
        if ($tahun) $q->where('YEAR(tanggal)',  $tahun);
        return (float) ($q->selectSum('jumlah','total')->get()->getRowArray()['total'] ?? 0);
    }

    /** Total pengeluaran periode */
    public function getTotalPengeluaran(?int $bulan = null, ?int $tahun = null): float
    {
        $q = $this->where('tipe', 'pengeluaran')->where('status', 'posted');
        if ($bulan) $q->where('MONTH(tanggal)', $bulan);
        if ($tahun) $q->where('YEAR(tanggal)',  $tahun);
        return (float) ($q->selectSum('jumlah','total')->get()->getRowArray()['total'] ?? 0);
    }

    /** Data grafik 6 bulan terakhir */
    public function getGrafik6Bulan(): array
    {
        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $ts    = strtotime("-{$i} months");
            $bulan = (int) date('n', $ts);
            $tahun = (int) date('Y', $ts);
            $result[] = [
                'label'       => date('M Y', $ts),
                'pemasukan'   => $this->getTotalPemasukan($bulan, $tahun),
                'pengeluaran' => $this->getTotalPengeluaran($bulan, $tahun),
            ];
        }
        return $result;
    }
}
