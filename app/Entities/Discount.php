<?php

namespace App\Entities;

class Discount extends BaseEntity
{
    protected $simpleName = 'discount';

    protected $id;
    protected $store;
    protected $target;
    protected $target_id;
    protected $value;
    protected $type;
    protected $stock;
    protected $sold;

    protected $valid_at;
    protected $expired_at;

    protected $objects = [
        'store' => 'App\Entities\Store',
    ];

    protected $casts = [ 
        'id'            => 'int',
        'store'         => 'object',
        'target'        => 'string',
        'target_id'     => 'int',
        'value'         => 'int',
        'type'          => 'string',
        'stock'         => 'int',
        'sold'          => 'int',
        'valid_at'      => 'timestamp',
        'expired_at'    => 'timestamp'
    ];
}
