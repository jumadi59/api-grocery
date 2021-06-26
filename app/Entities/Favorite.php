<?php

namespace App\Entities;

class Favorite extends Product
{
    public function setAttributes(array $data)
    {
        $data['is_favorite'] = true;
        return parent::setAttributes($data);
    }
}
