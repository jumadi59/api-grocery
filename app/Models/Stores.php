<?php

namespace App\Models;

use CodeIgniter\Model;

class Stores extends Model
{
    protected $table = 'stores';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'icon', 'thumb', 'user_id', 'address_id', 'rating',
        'courier_active', 'description', 'is_support_cod', 'is_activated'
    ];
    protected $returnType    = 'App\Entities\Store';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function search($query, $limit, $offset)
    {
        return $this->builder()->select('
        a.id, a.name, a.icon, a.thumb, a.courier_active, a.is_support_cod, a.rating,
        b.province')->from('stores a')
            ->join('address b', 'b.id=a.address_id', 'left')->like('a.name', $query)
            ->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function stores($limit, $offset)
    {
        return $this->builder()->select('
        a.id, a.name, a.icon, a.thumb, a.courier_active, a.is_support_cod, a.created_at, a.user_id, a.is_activated,
        b.province,
        c.username, c.avatar, c.email, c.last_activity
        ')->from('stores a')
            ->join('address b', 'b.id=a.address_id', 'left')
            ->join('users c', 'c.id=a.user_id', 'left')
            ->groupBy('a.id')->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function count($query, $filters = null)
    {
        $builder = $this->builder();
        $builder->select('COUNT(id) as store_count');
        if ($query) $builder->like('name', $query);
        $query = $builder->get()->getRow();
        return $query == null ? 0 : (int) $query->store_count;
    }

    public function store($id)
    {
        return $this->builder()->select('
        a.id, a.name, a.icon, a.thumb, a.courier_active, a.description, a.is_support_cod, a.rating, a.user_id,
        b.city, b.province, b.street, b.subdistrict, b.postal_code, b.latitude, b.longitude,
        c.username, c.avatar, c.last_activity')
            ->from('stores a')
            ->join('address b', 'b.id=a.address_id', 'left')
            ->join('users c', 'c.id=a.user_id', 'left')
            ->groupBy('a.id')->where('a.id', $id)->get()->getRow(0, $this->returnType);
    }

    public function storeUser($uid)
    {
        return $this->builder()->select('
        a.*,
        b.city, b.province, b.street, b.subdistrict, b.postal_code, b.latitude, b.longitude,')
            ->from('stores a')
            ->join('address b', 'b.id=a.address_id', 'left')->where('a.user_id', $uid)->get()->getRow(0, $this->returnType);
    }
}
