<?php

namespace App\Entities;

class Address extends BaseEntity
{

    protected $simpleName = 'address';

    protected $id;
    protected $primary;
    protected $label;

    protected $street;
    protected $province;
    protected $city;
    protected $subdistrict;
    protected $postal_code;
    protected $latitude;
    protected $longitude;

    protected $shipping_name;
    protected $shipping_phone;

    protected $casts = [
        'id'                => 'int',
        'primary'           => 'boolean',
        'street'            => 'string',
        'province'          => 'string',
        'city'              => 'string',
        'subdistrict'       => 'string',
        'postal_code'       => 'int',
        'latitude'          => 'double',
        'longitude'         => 'double',
        'shipping_name'     => 'string',
        'shipping_phone'    => 'string',
    ];
}
