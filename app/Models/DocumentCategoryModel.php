<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentCategoryModel extends Model
{
    protected $table            = 'document_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'icon',
        'warna',
        'sort_order',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Get categories along with count of active documents
     */
    public function getCategoriesWithStats()
    {
        return $this->select('document_categories.*, COUNT(documents.id) as total_documents, COALESCE(SUM(documents.file_size), 0) as total_size')
            ->join('documents', 'documents.kategori_id = document_categories.id', 'left')
            ->groupBy('document_categories.id')
            ->orderBy('document_categories.sort_order', 'ASC')
            ->findAll();
    }
}
