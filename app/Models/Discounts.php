<?php

namespace App\Models;

class Discounts extends BaseModel
{
    protected $table = 'discounts';
    protected $primaryKey = 'id';
    protected $fieldSearch   = 'value';
    protected $allowedFields = ['store_id','target','target_id','value','type','stock','sold','valid_at','expired_at'
    ];
    protected $returnType    = 'App\Entities\Discount';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function discountTarget($target, $id)
    {
        return $this->builder()->select()
            ->where(['target' => $target, 'target_id' => $id])->get()->getRow(0, $this->returnType);
    }

    public function discount($id)
    {
        return $this->builder()->select()
            ->where('id', $id)->get()->getRow(0, $this->returnType);
    }

    public function timeFlashSale() {
        $validTime = date('Y-m-d');
        return $this->builder()->select('valid_at, expired_at')
        ->where(['LEFT(valid_at,' . strlen($validTime) . ')' => $validTime, 'type' => 'flash_sale'])
        ->groupBy('valid_at')->get()->getResultArray();
    }

    public function currentFlashSale() {
        $valid = date('Y-m-d');
        return $this->builder()->select()
        ->where(['LEFT(valid_at,' . strlen($valid) . ')' => $valid, 'type' => 'flash_sale'])
        ->groupBy('valid_at')->get()->getResultArray();
    }
}
