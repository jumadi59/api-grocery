<?php namespace App\Entities;

class Courier extends BaseEntity
{
    protected $simpleName = 'courier';

    protected $id;
    protected $code;
    protected $name;
    protected $description;
    protected $note;
    protected $etd;
    protected $service;
    protected $simple_name;
    protected $icon;
    protected $cost;
    protected $is_activated;
    protected $is_cod;

    protected $casts = [
        'id'            => 'int',
        'code'          => 'string',
        'name'          => 'string',
        'service'       => 'string',
        'simple_name'   => 'string',
        'icon'          => 'string',
        'cost'          => 'int',
        'is_activated'  => 'boolean',
        'is_cod'        => 'boolean',
        'description'   => 'string',
        'note'          => 'string',
        'etd'           => 'string'
    ];
}