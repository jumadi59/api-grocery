<?php

namespace App\Entities;

class Variant extends BaseEntity
{
    protected $simpleName = 'variant';

    protected $id;
    protected $name;
    protected $thumb_index;
    protected $price;
    protected $discount;

    protected $objects = [
        'discount'  => 'App\Entities\Discount',
    ];

    protected $casts = [
        'id'                => 'int',
        'name'              => 'string',
        'thumb_index'       => 'int',
        'price'             => 'int',
        'discount'          => 'object'
    ];

    public function setAttributes(array $data)
    {
        if (isset($data['valid_at']) && isset($data['expired_at'])) {
            if (!$this->expired($data['valid_at'], $data['expired_at'])) {
                helper('my_helper');
                unset_all($data, ['store_id', 'target', 'target_id', 'value', 'type', 'discount_stock', 'discount_sold', 'valid_at', 'expired_at']);
            }
        }
        return parent::setAttributes($data);
    }

    private function expired($valid, $expired)
    {
        $day            = strtotime(date('Y-m-d H:i:s'));
        $valid_date     = strtotime($valid);
        $expiry_date    = strtotime($expired);

        return $valid_date <= $day && $expiry_date > $day;
    }
}
