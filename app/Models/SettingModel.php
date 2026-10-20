<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'id';

    protected $allowedFields = ['class', 'key', 'value', 'type', 'context'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retrieve a setting value by key
     */
    public function getVal($key, $default = null)
    {
        $setting = $this->where('key', $key)->first();
        if ($setting) {
            return $setting['value'];
        }
        return $default;
    }

    /**
     * Store or update a setting value
     */
    public function setVal($key, $value)
    {
        $setting = $this->where('key', $key)->first();
        if ($setting) {
            $this->update($setting['id'], [
                'value' => $value
            ]);
        } else {
            $this->insert([
                'class'   => 'web',
                'key'     => $key,
                'value'   => $value,
                'type'    => 'string',
                'context' => null
            ]);
        }
    }
}
