<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFormModel extends Model
{
    protected $table            = 'custom_forms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'description',
        'header_image',
        'is_public',
        'is_active',
        'closed_at',
        'allow_multiple',
        'auto_link_member',
        'success_message',
        'created_by',
    ];

    /**
     * Get form with active fields ordered by sort_order
     */
    public function getFormWithFields(string $slugOrId): ?array
    {
        $builder = is_numeric($slugOrId)
            ? $this->where('id', $slugOrId)
            : $this->where('slug', $slugOrId);

        $form = $builder->first();
        if (!$form) {
            return null;
        }

        $fieldModel = new CustomFormFieldModel();
        $form['fields'] = $fieldModel
            ->where('form_id', $form['id'])
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return $form;
    }

    /**
     * Get all forms with response counts
     */
    public function getFormsWithStats(): array
    {
        return $this->select('custom_forms.*, COUNT(r.id) as response_count')
            ->join('custom_form_responses r', 'r.form_id = custom_forms.id', 'left')
            ->groupBy('custom_forms.id')
            ->orderBy('custom_forms.id', 'DESC')
            ->findAll();
    }
}
