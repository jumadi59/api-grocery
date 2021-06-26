<?php

namespace App\Models;

use CodeIgniter\Model;

class Setting extends Model
{
    protected $table = 'setting';
    protected $allowedFields = [
        'app_name',
        'address',
        'app_id',
        'api_key_rajaongkir',
        'api_key_midtrans_server',
        'api_key_midtrans_client',
        'api_key_fcm',
        'is_production',
        'is_sigle_store',
    ];
    protected $returnType    = 'App\Entities\Setting';

    protected $useTimestamps = false;
    protected $skipValidation     = false;

    public function getSetting()
    {
        $query = $this->builder()->select()->get()->getRow();
        $entity = new $this->returnType;
        if ($query) {
            $entity->setAttributes(get_object_vars($query));
            return $entity;
        } else {
            return null;
        }
    }
}
