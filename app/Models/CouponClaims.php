<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponClaims extends Model
{
    protected $table = 'coupon_claims';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'coupon_id', 'is_used'];
    protected $returnType    = 'App\Entities\Coupon';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function coupons($uid, $get = null)
    {
        $this->builder()->select('a.is_used, b.*, 
        c.icon as store_icon, c.thumb as store_thumb, c.name as store_name')->from('coupon_claims a')
            ->join('coupons b', 'b.id=a.coupon_id')
            ->join('stores c', 'c.id=b.store_id', 'left');
        if (isset($get) && $get == 'toko') {
            $this->builder->where(['a.user_id' => $uid, 'b.store_id >' => 0]);
        } else if (isset($get) && $get == 'app') {
            $this->builder->where(['a.user_id' => $uid, 'b.store_id' => null]);
        } else {
            $this->builder->where('a.user_id', $uid);
        }

        return $this->builder->groupBy('a.id')->get()->getResult($this->returnType);
    }
}
