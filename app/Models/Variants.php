<?php

namespace App\Models;

class Variants extends BaseModel
{
    protected $table = 'variants';
    protected $primaryKey = 'id';
    protected $fieldSearch = 'name';
    protected $allowedFields = ['product_id', 'name', 'thumb_index', 'price'];
    protected $returnType    = 'App\Entities\Variant';

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function variants($productId)
    {
        return $this->builder()->select('a.*, 
        b.type, b.value, b.stock as discount_stock, b.sold as discount_sold, b.valid_at, b.expired_at,')->from('variants a')
            ->join('discounts b', "b.target_id=a.id AND b.target='variant'", 'left')
            ->where(['a.product_id' => $productId])
            ->groupBy('a.id')
            ->get()->getResult($this->returnType);
    }

    public function variant($id)
    {
        return $this->builder()->select()
            ->where('id', $id)->get()->getRow(0, $this->returnType);
    }

    public function variantAllFromStore($storeId) {
        return $this->builder()->select('a.*, 
        b.type, b.value, b.stock as discount_stock, b.sold as discount_sold, b.valid_at, b.expired_at,')->from('variants a')
            ->join('discounts b', "b.target_id=a.id AND b.target='variant'", 'left')
            ->where(['b.store_id' => $storeId])
            ->groupBy('a.id')
            ->get()->getResult($this->returnType);
    }

    public function deleteFromProduct($pid) {
        return $this->builder()->delete(['product_id', $pid]);
    }
}
