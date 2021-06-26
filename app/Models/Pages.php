<?php

namespace App\Models;

class Pages extends BaseModel
{
    protected $table = 'pages';
    protected $primaryKey = 'id';
    protected $fieldSearch = 'name';
    protected $allowedFields = ['name', 'thumb', 'description', 'link', 'content', 'is_activated',];
    protected $returnType    = 'App\Entities\Page';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function page($id)
    {
        return $this->builder()->select()
            ->where('id', $id)->get()->getRow(0, $this->returnType);
    }

    public function search($query)
    {
        $this->builder()->select('name,thumb,description,link, is_activated')->like($this->fieldSearch, $query);
        return $this;
    }
}
