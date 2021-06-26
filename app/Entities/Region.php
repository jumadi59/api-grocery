<?php namespace App\Entities;

class Region extends BaseEntity
{
    protected $id;
    protected $name;
    protected $province_id;
    protected $city_id;
    protected $postal_code;
    protected $type;
    
    protected $casts = [
        'id'            => 'int',
        'name'          => 'string',
        'province_id'   => 'int',
        'city_id'       => 'int',
        'postal_code'   => 'int',
        'type'          => 'string'
    ];
}