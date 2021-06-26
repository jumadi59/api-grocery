<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $fieldSearch;

    public function count($query)
    {
        $builder = $this->builder();
        $builder->select('COUNT(id) as count');
        if ($query) $builder->like($this->fieldSearch, $query);
        $query = $builder->get()->getRow();
        return $query == null ? 0 : (int) $query->count;
    }

    public function getResult()
    {
        return $this->builder->get()->getResult($this->returnType);
    }

    public function getRow()
    {
        return $this->builder->get()->getRow(0, $this->returnType);
    }

    public function search($query)
    {
        $this->builder()->select()->like($this->fieldSearch, $query);
        return $this;
    }

    public function where($data)
    {
        $this->builder()->select()->where($data);
        return $this;
    }

    public function limit($limit, $offset)
    {
        $this->builder->limit($limit, $offset);
        return $this;
    }

    public function sort($sort)
    {
        if (!empty($sort)) {
            $ex = explode('.', $sort);
            $this->builder->orderBy($ex[0], $ex[1]);
        }
        return $this;
    }
}
