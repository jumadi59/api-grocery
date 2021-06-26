<?php

namespace App\Entities;

class Store extends BaseEntity
{

    protected $simpleName = 'store';

    protected $id;
    protected $name;
    protected $icon;
    protected $thumb;
    protected $user;
    protected $rating;
    protected $courier_active;
    protected $description;
    protected $address;

    protected $created_at;
    protected $updated_at;

    protected $objects = [
        'address'   => 'App\Entities\Address',
        'user'      => 'App\Entities\User',
    ];

    protected $casts = [
        'id'                => 'int',
        'name'              => 'string',
        'icon'              => 'string',
        'thumb'             => 'string',
        'courier_active'    => 'array',
        'description'       => 'string',
        'user'              => 'object',
        'address'           => 'object',
        'rating'            => 'float'
    ];

    public function setAttributes(array $data)
    {

        if (isset($data['courier_active'])) {
            $data['courier_active'] = explode(',', $data['courier_active']);
        }
        return parent::setAttributes($data);
    }
}
