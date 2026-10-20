<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFormAnswerModel extends Model
{
    protected $table            = 'custom_form_answers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'response_id',
        'field_id',
        'answer_value',
    ];

    /**
     * Get answers for a response joined with field definitions
     */
    public function getAnswersByResponse(int $responseId): array
    {
        return $this->select('custom_form_answers.*, f.field_label, f.field_name, f.field_type, f.sort_order')
            ->join('custom_form_fields f', 'f.id = custom_form_answers.field_id', 'left')
            ->where('custom_form_answers.response_id', $responseId)
            ->orderBy('f.sort_order', 'ASC')
            ->orderBy('f.id', 'ASC')
            ->findAll();
    }
}
