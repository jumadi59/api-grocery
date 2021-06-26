<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItems extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'product_id', 'variant_id', 'order_id', 'quantity', 'price', 'name', 'thumb', 'discount', 'note',
    ];
    protected $returnType    = 'App\Entities\OrderItem';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function order_items($orderId)
    {
        return $this->builder()->select('
        a.*, 
        b.store_id,
        c.name as store_name, c.icon as store_icon')->from('order_items a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('stores c', 'c.id=b.store_id', 'left')
            ->groupBy('a.id')
            ->where('a.order_id', $orderId)->get()->getResult($this->returnType);
    }

    public function order_item($id)
    {
        return $this->builder()->select('
        a.*, 
        b.store_id, b.rating,
        c.name as store_name, c.icon as store_icon')->from('order_items a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('stores c', 'c.id=b.store_id', 'left')
            ->groupBy('a.id')
            ->where('a.id', $id)->get()->getRow(0, $this->returnType);
    }

    public function userReviewOrderItems($uid)
    {
        return $this->builder()->select('
        a.id, a.quantity, a.product_id, a.name as product_name, a.thumb as product_thumb, 
        c.store_id, 
        d.name as store_name, d.icon as store_icon')->from('order_items a')
            ->join('orders b', 'b.id=a.order_id', 'left')
            ->join('products c', 'c.id=a.product_id', 'left')
            ->join('stores d', 'd.id=c.store_id', 'left')
            ->join('transactions e', 'e.id=b.transaction_id', 'left')
            ->groupBy('a.id')->where(['e.user_id' => $uid, 'b.status' => 'done'])->get()->getResult($this->returnType);
    }

    public function witReviews($uid)
    {
        $query = $this->builder()->select('a.id')->from('order_items a')
            ->join('orders b', 'b.id=a.order_id', 'left')
            ->join('products c', 'c.id=a.product_id', 'left')
            ->join('stores d', 'd.id=c.store_id', 'left')
            ->join('transactions e', 'e.id=b.transaction_id', 'left')
            ->groupBy('a.id')->where(['e.user_id' => $uid, 'b.status' => 'done'])->get()->getResultArray();
        return count($query);
    }

    public function getStore($orderId)
    {
        return $this->builder()->select('b.store_id,
        c.name as store_name, c.icon as store_icon')->from('order_items a')
            ->join('products b', 'b.id=a.product_id', 'left')
            ->join('stores c', 'c.id=b.store_id', 'left')
            ->groupBy('b.store_id')
            ->where('a.order_id', $orderId)->get()->getRow(0, $this->returnType);
    }
}
