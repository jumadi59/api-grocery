<?php

namespace App\Entities;

class Payment extends BaseEntity
{
    protected $simpleName = 'payment';

    protected $id;
    protected $code;
    protected $name;
    protected $type;
    protected $service;
    protected $type_name;
    protected $icon;
    protected $is_activated;
    protected $fee;
    protected $note;
    protected $description;

    protected $casts = [
        'id'            => 'int',
        'code'          => 'string',
        'name'          => 'string',
        'type'          => 'string',
        'service'       => 'string',
        'type_name'     => 'string',
        'icon'          => 'string',
        'fee'           => 'int',
        'is_activated'  => 'boolean',
        'note'          => 'string',
        'description'   => 'string',
    ];
}
