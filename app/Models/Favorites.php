<?php

namespace App\Models;

use CodeIgniter\Model;

class Favorites extends Model
{
    protected $table = 'favorites';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'user_id'];
    protected $returnType    = 'App\Entities\Favorite';

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function favorites($userId)
    {
        return $this->builder()->select('
        a.product_id, 
        b.id, b.name, b.price, b.thumb, b.stock, b.unit, b.category_id, b.min_order, b.rating, b.is_free_shipping, b.is_cod,
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        d.name as store_name, d.icon as store_icon,
        e.city, e.latitude, e.longitude')->from('favorites a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('discounts c', "c.target_id=a.id AND c.target='product'", 'left')
            ->join('stores d', 'd.id=b.store_id', 'left')
            ->join('address e', 'e.id=d.address_id', 'left')
            ->groupBy('a.product_id')->where('a.user_id', $userId)->get()->getResult($this->returnType);
    }

    public function favorite($pid, $id)
    {
        return $this->builder()->select('
        a.product_id, 
        b.name, b.price, b.thumb, b.stock, b.sold, b.unit, b.category_id, b.min_order, b.rating, b.is_free_shipping, b.is_cod,
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        d.name as store_name, d.icon as store_icon,
        e.city, e.latitude, e.longitude')->from('favorites a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('discounts c', "c.target_id=a.id AND c.target='product'", 'left')
            ->join('stores d', 'd.id=b.store_id', 'left')
            ->join('address e', 'e.id=d.address_id', 'left')
            ->where(['a.user_id' => $id, 'a.product_id' => $pid])->get()->getRow(0, $this->returnType);
    }
}
