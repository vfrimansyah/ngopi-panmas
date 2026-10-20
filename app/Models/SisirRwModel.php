<?php

namespace App\Models;

use CodeIgniter\Model;

class SisirRwModel extends Model
{
    protected $table         = 'pemilu_sisir_rw';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nama_kegiatan', 'tanggal', 'alamat', 'rt', 'rw',
        'village_id', 'kelurahan', 'kecamatan',
        'bentuk_kegiatan_id', 'jumlah_peserta',
        'tokoh_hadir', 'agenda_lanjutan', 'keterangan',
        'status', 'created_by',
    ];

    protected $validationRules = [
        'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
        'status'        => 'required|in_list[hijau,kuning,merah]',
    ];

    /**
     * Get list with joined bentuk_kegiatan name + kelurahan
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->db->table('pemilu_sisir_rw sr')
            ->select('sr.*, bk.nama AS bentuk_kegiatan')
            ->join('pemilu_bentuk_kegiatan bk', 'bk.id = sr.bentuk_kegiatan_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('sr.status', $filters['status']);
        }
        if (!empty($filters['village_id'])) {
            $builder->where('sr.village_id', $filters['village_id']);
        }
        if (!empty($filters['bentuk_kegiatan_id'])) {
            $builder->where('sr.bentuk_kegiatan_id', $filters['bentuk_kegiatan_id']);
        }
        if (!empty($filters['rw'])) {
            $builder->where('sr.rw', $filters['rw']);
        }

        return $builder->orderBy('sr.tanggal', 'DESC')
                       ->orderBy('sr.id', 'DESC')
                       ->get()->getResultArray();
    }

    /**
     * Get single row with joined data
     */
    public function getDetail(int $id): ?array
    {
        $row = $this->db->table('pemilu_sisir_rw sr')
            ->select('sr.*, bk.nama AS bentuk_kegiatan')
            ->join('pemilu_bentuk_kegiatan bk', 'bk.id = sr.bentuk_kegiatan_id', 'left')
            ->where('sr.id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }
}
