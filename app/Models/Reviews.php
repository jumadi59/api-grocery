<?php

namespace App\Models;

use CodeIgniter\Model;

class Reviews extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $allowedFields = ['order_item_id', 'user_id', 'review', 'rating'];
    protected $returnType    = 'App\Entities\Review';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function reviews($limit, $offset, $productId)
    {
        return $this->builder()->select('
        a.*, 
        b.username, b.avatar')->from('reviews a')
            ->join('users b', 'b.id=a.user_id', 'left')
            ->join('order_items d', 'd.id=a.order_item_id', 'left')
            ->groupBy('a.id')->where('d.product_id', $productId)
            ->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function storeProductReviews($sid, $limit, $offset)
    {
        return $this->builder()->select('
        a.*,
        b.username, b.avatar, 
        d.thumb, d.name')->from('reviews a')
            ->join('users b', 'b.id=a.user_id', 'left')
            ->join('order_items d', 'd.id=a.order_item_id', 'left')
            ->join('products e', 'e.id=d.product_id', 'left')
            ->groupBy('a.id')->where('e.store_id', $sid)
            ->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function count($productId)
    {
        $query = $this->builder()->select('a.id')->from('reviews a')
            ->join('order_items b', 'b.id=a.order_item_id', 'left')
            ->groupBy('a.id')->where('b.product_id', $productId)->get()->getResultArray();
        return count($query);
    }

    function countRatingProduct($orderItemId, $rating) {
        $query = $this->builder()->select('COUNT(id) as count')
        ->where(['order_item_id' => $orderItemId, 'rating' => $rating])->get()->getRow();
        return $query != null ? $query->count : 0;
    }

    public function storeProductReviewCount($storeId)
    {
        $query = $this->builder()->select('a.id')->from('reviews a')
            ->join('order_items b', 'b.id=a.order_item_id', 'left')
            ->join('products c', 'c.id=b.product_id', 'left')
            ->groupBy('a.id')->where('c.store_id', $storeId)->get()->getResultArray();
        return count($query);
    }

    public function userReviewOrderItems($limit, $offset, $uid)
    {
        return $this->builder()->select('
        a.*,
        b.product_id, b.name as product_name, b.thumb as product_thumb, 
        d.store_id, 
        e.name as store_name, e.icon as store_icon')->from('reviews a')
            ->join('order_items b', 'b.id=a.order_item_id', 'left')
            ->join('orders c', 'c.id=b.order_id', 'left')
            ->join('products d', 'd.id=b.product_id', 'left')
            ->join('stores e', 'e.id=d.store_id', 'left')
            ->groupBy('b.id')->where('a.user_id', $uid)
            ->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function review($id)
    {
        return $this->builder()->select('
        a.*,
        b.username, b.avatar, 
        d.product_id, d.name as product_name, d.thumb as product_thumb, 
        e.store_id, 
        f.name as store_name')->from('reviews a')
            ->join('users b', 'b.id=a.user_id', 'left')
            ->join('order_items d', 'd.id=a.order_item_id', 'left')
            ->join('products e', 'e.id=d.product_id', 'left')
            ->join('stores f', 'f.id=e.store_id', 'left')
            ->groupBy('a.id')->where('a.id', $id)->get()->getRow(0, $this->returnType);
    }

    public function insert($data = null, bool $returnID = true)
    {
        helper('my_helper');

        $ratingProduct = array(
            5 => $this->countRatingProduct($data['order_item_id'], 5) + ((int) $data['rating'] === 5 ? 1 : 0),
            4 => $this->countRatingProduct($data['order_item_id'], 4) + ((int) $data['rating'] === 4 ? 1 : 0),
            3 => $this->countRatingProduct($data['order_item_id'], 3) + ((int) $data['rating'] === 3 ? 1 : 0),
            2 => $this->countRatingProduct($data['order_item_id'], 2) + ((int) $data['rating'] === 2 ? 1 : 0),
            1 => $this->countRatingProduct($data['order_item_id'], 1) + ((int) $data['rating'] === 1 ? 1 : 0)
            );
        $orderItem = $this->db->table('order_items')->select('product_id')->where('id', $data['order_item_id'])->get()->getRow();
        $this->db->transStart();
        $id = parent::insert($data);
        $this->db->table('products')->update(['rating' => calcAverageRating($ratingProduct)], ['id' => $orderItem->product_id]);

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return null;
        } else {
            return $id;
        }
    }
}
