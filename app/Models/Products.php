<?php

namespace App\Models;

use CodeIgniter\Model;

class Products extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'price', 'thumb', 'description', 'weight', 'stock', 'unit', 'sold',
        'min_order', 'store_id', 'category_id', 'is_activated', 'is_free_shipping', 'is_cod'
    ];
    protected $aliasName = ['product' => 'a', 'discount' => 'c', 'category' => 'd', 'store' => 'b', 'address' => 'e', 'favorite' => 'f', 'ads' => 'g'];
    protected $returnType    = 'App\Entities\Product';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function search($search, $limit, $offset, $filters = null, $userId = null)
    {
        $this->builder()->select('
        a.id, a.name, a.price, a.thumb, a.stock, a.sold, a.unit, a.category_id, a.rating, a.min_order, a.is_free_shipping, a.is_cod,
        b.name as store_name, b.icon as store_icon, b.thumb as store_thumb,
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        e.city, e.latitude, e.longitude,
        f.id as favorite_id,
        g.id as ads_id,
        ')->from('products a');
        $this->joinStore()->joinDiscount()->joinAddress()->joinCategory()->joinFavorite($userId)->joinAds();
        $this->builder->groupBy('a.id');
        if ($filters) {
            $this->where($filters);
            $this->sort($filters);
        }
        return $this->builder->like('a.name', $search)->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function count($query, $filters = null): int
    {
        $this->builder()->select('a.id')->from('products a');
        $this->joinStore()->joinDiscount()->joinAddress()->joinCategory();
        $this->builder->groupBy('a.id');

        if ($filters) {
            $this->where($filters);
        }
        if ($query) {
            $this->builder->like('a.name', $query);
        }
        $query = $this->builder->get()->getResultArray();
        return $query == null ? 0 : (int) count($query);
    }

    public function countRatingStore($storeId, $rating)
    {
        $query = $this->builder()->select('COUNT(id) as count')
            ->where(['store_id' => $storeId, 'rating' => $rating])->get()->getRow();
        return $query != null ? $query->count : 0;
    }

    public function sugestion($search, $filters = null)
    {
        $this->builder()->select('a.name')->from('products a');
        $this->joinStore()->joinDiscount()->joinAddress()->joinCategory();
        $this->builder->groupBy('a.id');
        if ($filters) {
            $this->where($filters);
            $this->sort($filters);
        }
        $query = $this->builder->like('a.name', $search)->limit(10, 0)->get()->getResultArray();
        $sugestions = [];
        foreach ($query as $value) {
            array_push($sugestions, $value['name']);
        }
        return $sugestions;
    }

    public function products($limit, $offset, $filters = null, $userId = null)
    {
        $this->builder()->select('
        a.id, a.name, a.price, a.thumb, a.stock, a.sold, a.unit, a.category_id, a.min_order, a.rating, a.is_free_shipping, a.is_cod,
        b.name as store_name, b.icon as store_icon, b.thumb as store_thumb,
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        e.city, e.latitude, e.longitude,
        f.id as favorite_id,
        g.id as ads_id,
        ')->from('products a');
        $this->joinStore()->joinDiscount()->joinAddress()->joinCategory()->joinFavorite($userId)->joinAds();
        $this->builder->groupBy('a.id');
        if ($filters) {
            $this->where($filters);
            $this->sort($filters);
        }
        return $this->builder->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function productStore($limit, $offset, $filters = null, $query = null)
    {
        $this->builder()->select("a.*")->from('products a')->groupBy('a.id');
        if ($filters) {
            $this->where($filters);
            $this->sort($filters);
        }
        if ($query) {
            $this->builder->like('a.name', $query);
        }
        return $this->builder->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function product($id, $userId = null)
    {
        $this->builder()->select('
        a.*,
        b.name as store_name, b.icon as store_icon, b.user_id, b.rating as store_rating,
        c.type, c.value, c.stock as discount_stock, c.sold as discount_sold, c.valid_at, c.expired_at,
        d.name as category_name, d.icon as category_icon, 
        e.city, e.province, e.street, e.subdistrict, e.postal_code, e.latitude, e.longitude,
        f.id as favorite_id,
        g.id as ads_id,
        h.username, h.avatar, h.last_activity
        ')->from('products a');
        $this->joinStore()->joinDiscount()->joinAddress()->joinCategory()->joinFavorite($userId)->joinAds()->joinStoreUser();
        $this->builder->groupBy('a.id');
        return $this->builder->where(['a.id' => $id])->get()->getRow(0, $this->returnType);
    }

    public function joinFavorite($userId)
    {
        $this->builder->join('favorites f', "f.product_id=a.id AND f.user_id='" . $userId . "'", 'left');
        return $this;
    }

    public function joinDiscount()
    {
        $this->builder->join('discounts c', "c.target_id=a.id AND c.target='product'", 'left');
        return $this;
    }

    public function joinCategory()
    {
        $this->builder->join('categories d', 'd.id=a.category_id OR d.parent=a.category_id', 'left');
        return $this;
    }

    public function joinAddress()
    {
        $this->builder->join('address e', 'e.id=b.address_id', 'left');
        return $this;
    }

    public function joinStore()
    {
        $this->builder->join('stores b', 'b.id=a.store_id', 'left');
        return $this;
    }

    public function joinAds()
    {
        $this->builder->join('ads g', "g.ads='product' AND g.action=a.id", 'left');
        return $this;
    }

    public function joinStoreUser()
    {
        $this->builder->join('users h', 'h.id=b.user_id', 'left');
        return $this;
    }

    protected function where($filters)
    {
        helper('my_helper');
        $wheres = [];
        if (isset($filters['category']) && !empty($filters['category'])) {
            $wheres[is_numeric($filters['category']) ? 'a.category_id' : 'd.name'] = $filters['category'];
        }
        if (isset($filters['parent_category']) && !empty($filters['parent_category'])) {
            $wheres[is_numeric($filters['parent_category']) ? 'd.parent' : 'd.name'] = $filters['parent_category'];
        }
        if (isset($filters['store']) && !empty($filters['store'])) {
            $wheres[is_numeric($filters['store']) ? 'a.store_id' : 'b.name'] = $filters['store'];
        }
        if (isset($filters['discount']) && !empty($filters['discount'])) {
            $wheres['c.type'] = 'promo';
            $wheres['c.value >='] = 1;
            $wheres['c.valid_at <='] = date('Y-m-d H:i:s');
            $wheres['c.expired_at >'] = date('Y-m-d H:i:s');
            unset($filters['discount']);
        }

        $noWhere = ['sort' , 'category', 'store', 'get', 'parent_category'];
        foreach ($filters as $key => $value) {
            if (!isKey($key, $noWhere) && $value) {
                $text = $key;
                foreach ($this->aliasName as $t => $a) {
                    $text = preg_replace("~\b$t\b~", $a, $text);
                }
                $wheres[$text] = $value;
            }
        }
        $this->builder->where($wheres);
        return $this;
    }

    protected function sort($filters)
    {
        helper('array_helper');
        if (isset($filters['sort']) && !empty($filters['sort'])) {
            $ex = explode('.', $filters['sort']);
            $isAlias = dot_array_search($ex[0], $this->aliasName);
            if ($isAlias && count($ex) === 3) {
                $this->builder->orderBy($this->aliasName[$ex[0]] . '.' . $ex[1], $ex[2]);
            } else {
                $this->builder->orderBy('a.' . $ex[0], $ex[1]);
            }
        }
        return $this;
    }
}
