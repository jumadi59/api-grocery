<?php

namespace App\Entities;

class OrderItem extends BaseEntity
{
    protected $simpleName = 'order_item';

    protected $id;
    protected $product;
    protected $order;
    protected $variant_id;
    protected $quantity;
    protected $note;

    protected $objects = [
        'product' => 'App\Entities\Product',
    ];

    protected $casts = [
        'id'        => 'int',
        'product'   => 'object',
        'quantity'  => 'int',
        'variant_id'  => 'int',
        'note'      => 'string',
    ];
}
