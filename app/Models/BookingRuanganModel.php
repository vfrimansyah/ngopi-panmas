<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingRuanganModel extends Model
{
    protected $table         = 'booking_ruangan';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'tgl_kegiatan',
        'jam_mulai',
        'jam_selesai',
        'nama_kegiatan',
        'penyelenggara',
        'ruangan_id',
        'pj_kegiatan',
        'catatan',
        'created_by',
    ];

    protected $validationRules = [
        'tgl_kegiatan'  => 'required|valid_date',
        'jam_mulai'     => 'required',
        'jam_selesai'   => 'required',
        'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
        'ruangan_id'    => 'required|integer',
    ];

    /**
     * Get booking list with room name join
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->db->table('booking_ruangan b')
            ->select('b.*, r.nama_ruangan')
            ->join('booking_ruangan_master r', 'r.id = b.ruangan_id', 'left');

        if (!empty($filters['from_date'])) {
            $builder->where('b.tgl_kegiatan >=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $builder->where('b.tgl_kegiatan <=', $filters['to_date']);
        }
        if (!empty($filters['ruangan_id'])) {
            $builder->where('b.ruangan_id', $filters['ruangan_id']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('b.nama_kegiatan', $s)
                ->orLike('b.penyelenggara', $s)
                ->orLike('b.pj_kegiatan', $s)
                ->orLike('b.catatan', $s)
                ->orLike('r.nama_ruangan', $s)
                ->groupEnd();
        }

        return $builder->orderBy('b.tgl_kegiatan', 'DESC')
                       ->orderBy('b.jam_mulai', 'DESC')
                       ->orderBy('b.id', 'DESC')
                       ->get()->getResultArray();
    }
}
