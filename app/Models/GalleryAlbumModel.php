<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryAlbumModel extends Model
{
    protected $table            = 'gallery_albums';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nama_album',
        'slug',
        'deskripsi',
        'cover_image',
        'tanggal_kegiatan',
        'lokasi',
        'sort_order',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil album beserta jumlah foto dan video di dalamnya
     */
    public function getAlbumsWithStats(): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('gallery_albums a')
            ->select('a.*, 
                COUNT(i.id) AS total_items,
                SUM(CASE WHEN i.tipe = "image" THEN 1 ELSE 0 END) AS total_images,
                SUM(CASE WHEN i.tipe = "video" THEN 1 ELSE 0 END) AS total_videos')
            ->join('gallery_items i', 'i.album_id = a.id', 'left')
            ->groupBy('a.id')
            ->orderBy('a.sort_order', 'ASC')
            ->orderBy('a.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}
