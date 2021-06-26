<?php

namespace App\Entities;

class Review extends BaseEntity
{
    protected $simpleName = 'review';

    protected $id;
    protected $order_item;
    protected $user;
    protected $review;
    protected $rating;

    protected $created_at;
    protected $updated_at;

    protected $objects = [
        'user'          => 'App\Entities\User',
        'order_items'   => 'App\Entities\OrderItem',
    ];

    protected $casts = [
        'id'            => 'int',
        'order_item'    => 'object',
        'user'          => 'object',
        'review'        => 'string',
        'rating'        => 'int'
    ];
}
