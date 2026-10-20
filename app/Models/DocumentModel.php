<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table            = 'documents';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'kategori_id',
        'nomor_dokumen',
        'judul',
        'slug',
        'deskripsi',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'tahun',
        'tanggal_dokumen',
        'sifat_dokumen',
        'bidang_kode',
        'institusi',
        'uploader_id',
        'uploader_nama',
        'views_count',
        'downloads_count',
        'is_pinned',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Get list of documents with category name & filters
     */
    public function getList(array $filters = [], int $perPage = 0)
    {
        $builder = $this->select('documents.*, document_categories.nama_kategori, document_categories.icon as kategori_icon, document_categories.warna as kategori_warna')
            ->join('document_categories', 'document_categories.id = documents.kategori_id', 'left');

        if (!empty($filters['kategori_id'])) {
            $builder->where('documents.kategori_id', $filters['kategori_id']);
        }

        if (!empty($filters['sifat_dokumen'])) {
            $builder->where('documents.sifat_dokumen', $filters['sifat_dokumen']);
        }

        if (!empty($filters['bidang_kode'])) {
            $builder->where('documents.bidang_kode', $filters['bidang_kode']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('documents.tahun', $filters['tahun']);
        }

        if (!empty($filters['file_type'])) {
            $builder->where('documents.file_type', $filters['file_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('documents.judul', $search)
                ->orLike('documents.nomor_dokumen', $search)
                ->orLike('documents.deskripsi', $search)
                ->orLike('documents.file_name', $search)
                ->orLike('documents.uploader_nama', $search)
                ->groupEnd();
        }

        $builder->orderBy('documents.is_pinned', 'DESC')
            ->orderBy('documents.id', 'DESC');

        if ($perPage > 0) {
            return $this->paginate($perPage);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get document detail with category info
     */
    public function getDetail(int $id)
    {
        return $this->select('documents.*, document_categories.nama_kategori, document_categories.icon as kategori_icon, document_categories.warna as kategori_warna')
            ->join('document_categories', 'document_categories.id = documents.kategori_id', 'left')
            ->where('documents.id', $id)
            ->first();
    }

    /**
     * Increment view counter
     */
    public function incrementViews(int $id)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->set('views_count', 'views_count + 1', false)
            ->update();
    }

    /**
     * Increment download counter
     */
    public function incrementDownloads(int $id)
    {
        return $this->db->table($this->table)
            ->where('id', $id)
            ->set('downloads_count', 'downloads_count + 1', false)
            ->update();
    }

    /**
     * Get summary stats for dashboard
     */
    public function getStats()
    {
        $totalDocs     = $this->countAllResults();
        $totalSize     = $this->selectSum('file_size')->first()['file_size'] ?? 0;
        $totalPublik   = $this->where('sifat_dokumen', 'publik')->countAllResults();
        $totalInternal = $this->where('sifat_dokumen', 'internal')->countAllResults();
        $totalRahasia  = $this->where('sifat_dokumen', 'rahasia')->countAllResults();
        $totalViews    = $this->selectSum('views_count')->first()['views_count'] ?? 0;
        $totalDownloads= $this->selectSum('downloads_count')->first()['downloads_count'] ?? 0;

        // Group by file_type
        $byType = $this->select('file_type, COUNT(id) as total')
            ->groupBy('file_type')
            ->orderBy('total', 'DESC')
            ->findAll();

        return [
            'total_docs'      => $totalDocs,
            'total_size'      => $totalSize,
            'total_publik'    => $totalPublik,
            'total_internal'  => $totalInternal,
            'total_rahasia'   => $totalRahasia,
            'total_views'     => $totalViews,
            'total_downloads' => $totalDownloads,
            'by_type'         => $byType,
        ];
    }
}
