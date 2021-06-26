<?php

namespace App\Entities;

class Page extends BaseEntity
{
    protected $simpleName = 'page';

    protected $id;
    protected $name;
    protected $thumb;
    protected $description;
    protected $link;
    protected $content;
    protected $is_activated;

    protected $created_at;
    protected $updated_at;

    protected $casts = [
        'id'            => 'int',
        'name'          => 'string',
        'thumb'         => 'string',
        'description'   => 'string',
        'link'          => 'string',
        'content'       => 'string',
        'is_activated'  => 'boolean',
    ];
}
