<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryItemModel extends Model
{
    protected $table            = 'gallery_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'album_id',
        'tipe',
        'judul',
        'slug',
        'deskripsi',
        'file_path',
        'file_name',
        'file_size',
        'video_type',
        'video_url',
        'video_id',
        'thumbnail_path',
        'tanggal_media',
        'lokasi',
        'kredit',
        'is_featured',
        'views_count',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil list media dengan join ke album dan filter pencarian
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->builder();
        $builder->select('gallery_items.*, gallery_albums.nama_album, gallery_albums.slug AS album_slug, users.username AS uploader_username')
            ->join('gallery_albums', 'gallery_albums.id = gallery_items.album_id', 'left')
            ->join('users', 'users.id = gallery_items.created_by', 'left');

        if (!empty($filters['tipe'])) {
            $builder->where('gallery_items.tipe', $filters['tipe']);
        }

        if (!empty($filters['album_id'])) {
            $builder->where('gallery_items.album_id', (int)$filters['album_id']);
        }

        if (!empty($filters['is_featured'])) {
            $builder->where('gallery_items.is_featured', 1);
        }

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('gallery_items.judul', $s)
                ->orLike('gallery_items.deskripsi', $s)
                ->orLike('gallery_items.lokasi', $s)
                ->orLike('gallery_items.kredit', $s)
                ->orLike('gallery_albums.nama_album', $s)
            ->groupEnd();
        }

        $builder->orderBy('gallery_items.is_featured', 'DESC')
            ->orderBy('gallery_items.tanggal_media', 'DESC')
            ->orderBy('gallery_items.id', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Statistik total foto, total video, total storage, total views
     */
    public function getStats(): array
    {
        $db = \Config\Database::connect();
        $row = $db->table($this->table)->select('
            COUNT(id) AS total_media,
            SUM(CASE WHEN tipe = "image" THEN 1 ELSE 0 END) AS total_images,
            SUM(CASE WHEN tipe = "video" THEN 1 ELSE 0 END) AS total_videos,
            SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) AS total_featured,
            COALESCE(SUM(file_size), 0) AS total_storage_size,
            COALESCE(SUM(views_count), 0) AS total_views
        ')->get()->getRowArray();

        return [
            'total_media'        => (int)($row['total_media'] ?? 0),
            'total_images'       => (int)($row['total_images'] ?? 0),
            'total_videos'       => (int)($row['total_videos'] ?? 0),
            'total_featured'     => (int)($row['total_featured'] ?? 0),
            'total_storage_size' => (int)($row['total_storage_size'] ?? 0),
            'total_views'        => (int)($row['total_views'] ?? 0),
        ];
    }

    /**
     * Increment view counter
     */
    public function incrementViews(int $id): void
    {
        $this->builder()->where('id', $id)->increment('views_count');
    }
}
