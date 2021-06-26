<?php

namespace App\Entities;

class Rekening extends BaseEntity
{
    protected $simpleName = 'rekening';
    protected $id;
    protected $user;
    protected $bank;
    protected $no;
    protected $name;
    protected $created_at;
    protected $updated_at;

    protected $objects = [
        'user'   => 'App\Entities\User',
    ];

    protected $casts = [
        'id'        => 'int',
        'user'      => 'object',
        'bank'      => 'object',
        'no'        => 'int',
        'name'  => 'string'
    ];
}
