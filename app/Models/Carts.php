<?php

namespace App\Models;

use CodeIgniter\Model;

class Carts extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'variant_id', 'user_id', 'count'];
    protected $returnType    = 'App\Entities\Cart';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function count($userId)
    {
        $query = $this->builder()->select('COUNT(id) as cart_count')->where('user_id', $userId)->get();
        return (int) $query->getRow()->cart_count;
    }

    public function carts($uid)
    {
        return $this->builder()->select('
        a.id, a.count, a.variant_id, a.product_id,
        b.name, b.price, b.thumb, b.min_order, b.store_id, b.stock, b.unit, b.weight, 
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        d.name as store_name, d.icon as store_icon, 
        e.name as variant_name, e.thumb_index, e.price as variant_price')->from('carts a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('stores d', 'd.id=b.store_id', 'left')
            ->join('discounts c', "(c.target_id=a.product_id AND c.target='product') OR (c.target_id=a.variant_id AND c.target='variant')", 'left')
            ->join('variants e', 'e.id=a.variant_id', 'left')
            ->groupBy('a.id')->where('a.user_id', $uid)->get()->getResult($this->returnType);
    }

    public function cart($id)
    {
        return $this->builder()->select('
        a.id, a.count, a.variant_id, a.product_id,
        b.name, b.price, b.thumb, b.min_order, b.store_id, b.stock, b.unit, b.weight, 
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        d.name as store_name, d.icon as store_icon, 
        e.name as variant_name, e.thumb_index, e.price as variant_price')->from('carts a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('discounts c', "(c.target_id=a.product_id AND c.target='product') OR (c.target_id=a.variant_id AND c.target='variant')", 'left')
            ->join('stores d', 'd.id=b.store_id', 'left')
            ->join('variants e', 'e.id=a.variant_id', 'left')
            ->where('a.id', $id)->get()->getRow(0, $this->returnType);
    }
}
