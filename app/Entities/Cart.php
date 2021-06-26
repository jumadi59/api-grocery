<?php namespace App\Entities;

class Cart extends BaseEntity
{
    protected $simpleName = 'cart';

    protected $id;
    protected $product;
    protected $variant;
    protected $user;
    protected $count;
    
    protected $created_at;
    protected $updated_at;
    
    protected $objects = [
        'product'   => 'App\Entities\Product',
        'variant'   => 'App\Entities\Variant',
        'user'      => 'App\Entities\User',
    ];
    
    protected $casts = [
        'id'        => 'int',
        'product'   => 'object',
        'variant'   => 'object',
        'user'      => 'object',
        'count'     => 'int'
    ];
    
    public function setAttributes(array $data) {
        helper('my_helper');

        $thumbIndex = 0;
        $name = $data['name'];
        $price = $data['price'];
        if ((int) $data['variant_id'] > 0) {
            $thumbIndex = $data['thumb_index'];
            $price = $data['variant_price'];
            $name .= ' - ' . $data['variant_name'];
        } else {
            unset_all($data, ['variant_id', 'thumb_index', 'variant_price', 'variant_name',]);
        }

        $data['name'] = $name;
        $data['price'] = (int) $price;
        $data['thumb'] = explode(',', $data['thumb'])[$thumbIndex];

        return parent::setAttributes($data);
    }
}