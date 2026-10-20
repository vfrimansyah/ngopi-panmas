<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanListModel extends Model
{
    protected $table         = 'kegiatan_list';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'nama_kegiatan',
        'jenis_kegiatan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'penyelenggara',
        'pic',
        'jumlah_peserta',
        'lokasi_kegiatan',
        'dpra',
        'rw',
        'rt',
        'tokoh_hadir',
        'tokoh_pks',
        'catatan',
        'link_doc',
        'flyer',
        'status_kondisi',
        'created_by',
    ];

    protected $validationRules = [
        'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
    ];

    /**
     * Get kegiatan list with filter
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->db->table('kegiatan_list k')
            ->select('k.*, j.nama_kegiatan AS jenis_kegiatan_nama')
            ->join('kegiatan_jenis j', 'j.id = k.jenis_kegiatan_id', 'left');

        if (!empty($filters['from_date'])) {
            $builder->where('k.tanggal >=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $builder->where('k.tanggal <=', $filters['to_date']);
        }
        if (!empty($filters['penyelenggara'])) {
            $builder->where('k.penyelenggara', $filters['penyelenggara']);
        }
        if (!empty($filters['jenis_kegiatan_id'])) {
            $builder->where('k.jenis_kegiatan_id', $filters['jenis_kegiatan_id']);
        }
        if (!empty($filters['dpra'])) {
            $builder->where('k.dpra', $filters['dpra']);
        }
        if (!empty($filters['status_kondisi'])) {
            $builder->where('k.status_kondisi', $filters['status_kondisi']);
        }
        if (!empty($filters['rw'])) {
            $builder->where('k.rw', $filters['rw']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('k.nama_kegiatan', $s)
                ->orLike('k.penyelenggara', $s)
                ->orLike('k.pic', $s)
                ->orLike('k.lokasi_kegiatan', $s)
                ->orLike('k.catatan', $s)
                ->groupEnd();
        }

        return $builder->orderBy('k.tanggal', 'DESC')
                       ->orderBy('k.jam_mulai', 'DESC')
                       ->orderBy('k.id', 'DESC')
                       ->get()->getResultArray();
    }
}
