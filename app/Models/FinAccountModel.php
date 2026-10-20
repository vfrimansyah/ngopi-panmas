<?php
namespace App\Models;
use CodeIgniter\Model;

class FinAccountModel extends Model
{
    protected $table         = 'fin_accounts';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['kode_akun','nama_akun','tipe_akun','normal_balance','deskripsi','parent_id','is_active'];
    protected $useTimestamps = true;

    /** Akun aktif, diurutkan kode */
    public function getActive(): array
    {
        return $this->where('is_active', 1)->orderBy('kode_akun', 'ASC')->findAll();
    }

    /** Akun by tipe */
    public function getByTipe(string $tipe): array
    {
        return $this->where('tipe_akun', $tipe)->where('is_active', 1)->orderBy('kode_akun')->findAll();
    }

    /** Semua pendapatan */
    public function getPendapatan(): array { return $this->getByTipe('pendapatan'); }

    /** Semua beban */
    public function getBeban(): array { return $this->getByTipe('beban'); }

    /** Akun kas & bank */
    public function getKasDanBank(): array
    {
        return $this->whereIn('tipe_akun', ['kas','bank'])->where('is_active', 1)->orderBy('kode_akun')->findAll();
    }
}
