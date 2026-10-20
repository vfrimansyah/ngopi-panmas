<?php

namespace App\Models;

use CodeIgniter\Model;

class ProposalRabModel extends Model
{
    protected $table            = 'proposal_rabs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'proposal_id',
        'kategori',
        'uraian',
        'volume',
        'satuan',
        'harga_satuan',
        'total_harga',
        'keterangan',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all RAB items for a proposal, grouped or ordered
     */
    public function getByProposal(int $proposalId)
    {
        return $this->where('proposal_id', $proposalId)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Calculate and return total amount for a proposal
     */
    public function calculateTotal(int $proposalId): float
    {
        $res = $this->selectSum('total_harga')->where('proposal_id', $proposalId)->first();
        return (float)($res['total_harga'] ?? 0);
    }
}
