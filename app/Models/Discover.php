<?php

namespace App\Models;

class Discover extends BaseModel
{
    protected $table         = 'discover';
    protected $primaryKey    = 'id';
    protected $fieldSearch   = 'id';
    protected $allowedFields = ['title', 'subtitle', 'banner', 'banner_left', 'background_color', 'filters'];
    protected $returnType    = 'App\Entities\Discover';

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
