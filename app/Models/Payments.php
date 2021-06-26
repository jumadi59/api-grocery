<?php

namespace App\Models;

class Payments extends BaseModel
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $fieldSearch = 'name';
    protected $allowedFields = ['code', 'name', 'type', 'type_name', 'icon', 'fee', 'description', 'note', 'is_activated'];
    protected $returnType    = 'App\Entities\Payment';

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

}
