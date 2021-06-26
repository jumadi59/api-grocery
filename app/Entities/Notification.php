<?php namespace App\Entities;

class Notification extends BaseEntity
{
    protected $simpleName = 'notification';

    protected $id;
    protected $from;
    protected $to;
    protected $title;
    protected $message;
    protected $image;
    protected $action;
    protected $label;
    protected $is_read;
    
    protected $created_at;
    protected $updated_at;

    protected $casts = [
        'id'        => 'int',
        'from'      => 'string',
        'to'        => 'string',
        'label'     => 'string',
        'title'     => 'string',
        'message'   => 'string',
        'image'     => 'string',
        'action'    => 'string',
        'is_read'   => 'boolean'
    ];
}