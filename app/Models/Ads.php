<?php

namespace App\Models;

class Ads extends BaseModel
{
    protected $table         = 'ads';
    protected $primaryKey    = 'id';
    protected $fieldSearch   = 'title';
    protected $allowedFields = [
        'type', 'image', 'title', 'description', 'tag', 'key',
        'action', 'is_activated', 'expired_at', 'show', 'click'
    ];
    protected $returnType    = 'App\Entities\Ads';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function tag($data)
    {
        return $this->builder()->select()
            ->where(['expired_at >' => date('Y-m-d'), 'is_activated' => true])
            ->like('tag', implode(', ', $data))
            ->orderBy('created_at', 'asc')->get()->getLastRow($this->returnType);
    }
}
