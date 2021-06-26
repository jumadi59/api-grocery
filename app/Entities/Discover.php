<?php

namespace App\Entities;

class Discover extends BaseEntity
{
    protected $simpleName = 'discover';

    protected $id;
    protected $title;
    protected $sub_title;
    protected $banner;
    protected $banner_left;
    protected $background_color;
    protected $filters;

    protected $casts = [
        'id'                => 'string',
        'title'             => 'string',
        'sub_title'         => 'string',
        'banner'            => 'string',
        'banner_left'       => 'string',
        'background_color'  => 'string',
        'filters'           => 'array',
    ];

    public function setAttributes(array $data)
    {
        if (isset($data['filters'])) {
            $data['filters'] = json_decode($data['filters'], true);
        }
        return parent::setAttributes($data);
    }
}
