<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomFormFieldModel extends Model
{
    protected $table            = 'custom_form_fields';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'form_id',
        'field_label',
        'field_name',
        'field_type',
        'field_options',
        'default_value',
        'help_text',
        'is_required',
        'map_to_member_field',
        'sort_order',
    ];
}
