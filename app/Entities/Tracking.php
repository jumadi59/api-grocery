<?php

namespace App\Entities;

class Tracking extends BaseEntity
{
    protected $simpleName = 'tracking';

    protected $id;
    protected $order_id;
    protected $name;
    protected $description;
    protected $status;

    protected $created_at;

    protected $dates = [
        'created_at',
    ];

    protected $casts = [
        'id'            => 'int',
        'order_id'      => 'int',
        'name'          => 'string',
        'description'   => 'string',
        'status'        => 'boolean',
    ];
}
