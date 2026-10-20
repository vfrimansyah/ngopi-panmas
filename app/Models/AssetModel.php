<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table         = 'assets';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'kode_aset',
        'nama_barang',
        'merk',
        'model',
        'lokasi',
        'penanggung_jawab',
        'created_by',
        'jumlah',
        'kondisi',
        'keterangan',
        'foto',
    ];

    protected $validationRules = [
        'nama_barang' => 'required|min_length[2]|max_length[255]',
        'jumlah'      => 'required|is_natural_no_zero',
    ];

    /**
     * Get asset list with optional filters
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->builder();

        if (!empty($filters['lokasi'])) {
            $builder->where('lokasi', $filters['lokasi']);
        }
        if (!empty($filters['kondisi'])) {
            $builder->where('kondisi', $filters['kondisi']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('nama_barang', $s)
                ->orLike('kode_aset', $s)
                ->orLike('merk', $s)
                ->orLike('model', $s)
                ->orLike('penanggung_jawab', $s)
                ->orLike('keterangan', $s)
                ->groupEnd();
        }

        return $builder->orderBy('lokasi', 'ASC')
                       ->orderBy('nama_barang', 'ASC')
                       ->get()->getResultArray();
    }
}
