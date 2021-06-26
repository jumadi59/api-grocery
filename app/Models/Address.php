<?php

namespace App\Models;

use CodeIgniter\Model;

class Address extends Model
{
    protected $table = 'address';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'street', 'province', 'city', 'subdistrict', 'primary', 'label',
        'postal_code', 'latitude', 'longitude', 'shipping_phone', 'shipping_name'
    ];
    protected $returnType    = 'App\Entities\Address';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function address($userId)
    {
        return $this->builder()->where('user_id', $userId)->get()->getResult($this->returnType);
    }

    public function detail_address($id)
    {
        return $this->builder()->select()->where('id', $id)->get()->getRow(0, $this->returnType);
    }

    public function address_primary($userId)
    {
        return $this->builder()->select()
            ->where(['user_id' => $userId, 'primary' => true])->get()->getRow(0, $this->returnType);
    }
}
