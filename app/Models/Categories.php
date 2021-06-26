<?php

namespace App\Models;

class Categories extends BaseModel
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $fieldSearch   = 'name';
    protected $allowedFields = ['name', 'display_name', 'icon', 'thumb', 'parent', 'is_activated'];
    protected $returnType    = 'App\Entities\Category';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function category($id)
    {
        return $this->builder()->select()
            ->where('id', $id)->get()->getRow(0, $this->returnType);
    }
}
