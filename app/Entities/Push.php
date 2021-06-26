<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Push extends Entity
{
    protected $title;
    protected $is_background;
    protected $message;
    protected $image;
    protected $action;
    protected $payload;
    protected $timestamp;
    protected $casts = [
        'title'         => 'string',
        'is_background' => 'boolean',
        'message'       => 'string',
        'image'         => 'string',
        'action'        => 'string',
        'payload'       => 'object',
        'timestamp'     => 'string'
    ];
}
