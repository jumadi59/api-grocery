<?php

namespace App\Entities;

class Category extends BaseEntity
{
    protected $simpleName = 'category';

    protected $id;
    protected $parent;
    protected $name;
    protected $display_name;
    protected $icon;
    protected $thumb;
    protected $is_activated;

    protected $created_at;
    protected $updated_at;

    protected $casts = [
        'id'            => 'int',
        'parent'        => 'int',
        'name'          => 'string',
        'display_name'  => 'string',
        'icon'          => 'string',
        'thumb'         => 'string',
        'is_activated'  => 'boolean',
    ];
}
