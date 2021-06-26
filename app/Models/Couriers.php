<?php

namespace App\Models;


class Couriers extends BaseModel
{
    protected $table = 'couriers';
    protected $primaryKey = 'id';
    protected $fieldSearch   = 'name';
    protected $allowedFields = ['name', 'icon', 'simple_name', 'is_activated', 'is_cod'];
    protected $returnType    = 'App\Entities\Courier';

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
