<?php

namespace App\Models;

class Coupons extends BaseModel
{
    protected $table = 'coupons';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'store', 'banner', 'name', 'code', 'min_transaction', 'value', 'unit',
        'type', 'target', 'target_id', 'platfrom', 'stock', 'valid_at', 'expired_at',
    ];
    protected $returnType    = 'App\Entities\Coupon';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function coupons($storeId, $uid = null)
    {
        $this->builder()->select('a.*, b.is_used, b.id as claim_id')->from('coupons a');
        $this->joinCalim($uid);
        $this->builder->where('a.store_id', $storeId);
        return $this->builder->groupBy('a.id')->get()->getResult($this->returnType);
    }

    public function coupon($id, $uid = null)
    {
        $this->builder()->select('a.*,
        b.is_used, b.id as claim_id,
        c.name as store_name, c.thumb as store_thumb,')->from('coupons a');
        $this->joinCalim($uid);
        $this->builder->join('stores c', 'c.id=a.store_id', 'left')
            ->where('a.id', $id);

        return $this->builder->groupBy('a.id')->get()->getRow(0, $this->returnType);
    }


    public function joinCalim($uid)
    {
        $this->builder->join('coupon_claims b', "b.coupon_id=a.id AND b.user_id='" . $uid . "'", 'left');
        return $this;
    }
}
