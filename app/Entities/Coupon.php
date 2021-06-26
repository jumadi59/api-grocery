<?php

namespace App\Entities;

class Coupon extends BaseEntity
{
    protected $simpleName = 'coupon';

    protected $id;
    protected $store;
    protected $banner;
    protected $name;
    protected $description;
    protected $code;
    protected $min_transaction;
    protected $value;
    protected $unit;
    protected $type;
    protected $target;
    protected $target_id;
    protected $platfrom;
    protected $stock;

    protected $user_id;
    protected $is_claim;
    protected $is_used;
    protected $terms;

    protected $valid_at;
    protected $expired_at;

    protected $objects = [
        'store' => 'App\Entities\Store',
    ];

    protected $dates = [
        'expired_at',
        'valid_at'
    ];

    protected $casts = [ 
        'id'                => 'int',
        'store'             => 'object',
        'banner'            => 'string',
        'name'              => 'string',
        'description'       => 'string',
        'code'              => 'string',
        'min_transaction'   => 'int',
        'value'             => 'int',
        'unit'              => 'string',
        'type'              => 'string',
        'target'            => 'string',
        'target_id'         => 'int',
        'platfrom'          => 'string',
        'stock'             => 'int',
        'valid_at'          => 'datetime',
        'expired_at'        => 'datetime',
        'is_claim'          => 'boolean',
        'is_used'           => 'boolean',
    ];

    public function setAttributes(array $data)
    {
        if (isset($data['claim_id'])) {
            $data['is_claim'] = true;
        }
        return parent::setAttributes($data);
    }
}
