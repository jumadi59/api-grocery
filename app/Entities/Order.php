<?php

namespace App\Entities;

class Order extends BaseEntity
{
    protected $simpleName = 'order';

    protected $id;
    protected $transaction;
    protected $coupon;
    protected $courier;
    protected $subtotal;
    protected $invoice;
    protected $resi;
    protected $status;
    protected $order_items;
    protected $trackings;

    protected $sent_at;
    protected $cenceled_at;
    protected $accepted_at;
    protected $expired_at;

    protected $objects = [
        'transaction'   => 'App\Entities\Transaction',
        'coupon'       => 'App\Entities\Coupon',
        'order_items'   => 'App\Entities\OrderItem',
    ];

    protected $dates = [
        'expired_at',
        'accepted_at',
        'cenceled_at',
        'sent_at',
    ];

    protected $casts = [
        'id'            => 'int',
        'transaction'   => 'object',
        'coupon'        => 'object',
        'courier'       => 'json',
        'subtotal'      => 'int',
        'invoice'       => 'string',
        'resi'          => 'long',
        'status'        => 'string',
        'order_items'   => 'array?object',
        'trackings'     => 'array',
    ];

    public function setOrderItems($items)
    {
        $this->attributes['order_items'] = $items;
        return $this;
    }
}
