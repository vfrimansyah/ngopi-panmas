<?php
namespace App\Models;
use CodeIgniter\Model;

class FinJurnalModel extends Model
{
    protected $table         = 'fin_jurnal';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nomor_jurnal','tanggal','deskripsi','transaksi_id','created_by'];
    protected $useTimestamps = true;

    public function generateNomor(): string
    {
        $prefix = 'JRN-' . date('Ym') . '-';
        $last   = $this->like('nomor_jurnal', $prefix, 'after')->orderBy('id','DESC')->first();
        $seq    = $last ? (int) substr($last['nomor_jurnal'], -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getList(array $filter = []): array
    {
        $q = $this->db->table('fin_jurnal j')
            ->select('j.*, u.username AS created_by_name')
            ->join('users u', 'u.id = j.created_by', 'left');
        if (!empty($filter['bulan'])) $q->where('MONTH(j.tanggal)', $filter['bulan']);
        if (!empty($filter['tahun'])) $q->where('YEAR(j.tanggal)',  $filter['tahun']);
        return $q->orderBy('j.tanggal','DESC')->orderBy('j.id','DESC')->get()->getResultArray();
    }

    public function getWithDetail(int $id): ?array
    {
        $jurnal = $this->find($id);
        if (!$jurnal) return null;

        $details = $this->db->table('fin_jurnal_detail jd')
            ->select('jd.*, a.kode_akun, a.nama_akun, a.normal_balance')
            ->join('fin_accounts a', 'a.id = jd.akun_id', 'left')
            ->where('jd.jurnal_id', $id)
            ->get()->getResultArray();

        $jurnal['details'] = $details;
        return $jurnal;
    }
}

class FinJurnalDetailModel extends Model
{
    protected $table         = 'fin_jurnal_detail';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['jurnal_id','akun_id','deskripsi','debit','kredit'];
    protected $useTimestamps = false;
}
