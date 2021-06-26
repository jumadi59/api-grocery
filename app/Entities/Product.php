<?php

namespace App\Entities;

class Product extends BaseEntity
{
    protected $simpleName = 'product';

    protected $id;
    protected $name;
    protected $thumb;
    protected $thumbs;
    protected $min_order;
    protected $price;
    protected $description;
    protected $unit;
    protected $stock;
    protected $sold;
    protected $discount;
    protected $weight;
    protected $store;
    protected $category;
    protected $rating;
    protected $is_activated;
    protected $is_favorite;
    protected $is_free_shipping;
    protected $is_cod;
    protected $is_ads;

    protected $link;

    protected $created_at;
    protected $updated_at;

    protected $objects = [
        'store'     => 'App\Entities\Store',
        'category'  => 'App\Entities\Category',
        'discount'  => 'App\Entities\Discount',
    ];

    protected $casts = [
        'id'                => 'int',
        'name'              => 'string',
        'min_order'         => 'int',
        'price'             => 'int',
        'description'       => 'string',
        'unit'              => 'string',
        'stock'             => 'int',
        'sold'              => 'int',
        'category'          => 'object',
        'thumb'             => 'string',
        'thumbs'            => 'array',
        'discount'          => 'object',
        'weight'            => 'float',
        'store'             => 'object',
        'rating'            => 'float',
        'is_favorite'       => 'boolean',
        'is_activated'      => 'boolean',
        'is_free_shipping'  => 'boolean',
        'is_cod'            => 'boolean',
        'is_ads'            => 'boolean',
        'link'              => 'string',
    ];

    public function setAttributes(array $data)
    {
        if (isset($data['thumb'])) {
            $thumbs         = explode(',', $data['thumb']);
            $data['thumbs'] = $thumbs;
            $data['thumb']  = $thumbs[0];
        }
        if (isset($data['favorite_id'])) {
            $data['is_favorite']  = true;
        }
        if (isset($data['ads_id'])) {
            $data['is_ads']  = true;
        }

        if (isset($data['valid_at']) && isset($data['expired_at'])) {
            helper('my_helper');
            if (!isValid($data['valid_at'], $data['expired_at'])) {
                unset_all($data, ['target', 'target_id', 'value', 'type', 'discount_stock', 'discount_sold', 'valid_at', 'expired_at']);
            }
        }
        if (isset($data['name'])) {
            $data['link'] = getSlug($data['name']);
        } else if (isset($data['product_name'])) {
            $data['link'] = getSlug($data['product_name']);
        }
        
        return parent::setAttributes($data);
    }
}
