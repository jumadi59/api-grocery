<?php

namespace App\Entities;

class Ads extends BaseEntity
{
    protected $simpleName = 'ads';

    protected $id;
    protected $type;
    protected $key;
    protected $title;
    protected $ads;
    protected $tag;
    protected $action;
    protected $show;
    protected $click;
    protected $description;
    protected $image;
    protected $is_activated;
    protected $expired_at;

	protected $dates = [
		'created_at',
		'updated_at',
		'expired_at',
	];

    protected $casts = [ 
        'id'            => 'int',
        'type'          => 'string',
        'type'          => 'key',
        'ads'           => 'string',
        'image'         => 'string',
        'title'         => 'string',
        'description'   => 'string',
        'tag'           => 'string',
        'action'        => 'string',
        'is_activated'  => 'boolean',
        'expired_at'    => 'date',
        'show'          => 'int',
        'click'         => 'int'
    ];
}
