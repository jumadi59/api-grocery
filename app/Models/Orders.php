<?php

namespace App\Models;

class Orders extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'transaction_id', 'voucher_id', 'courier', 'invoice',
        'resi', 'status', 'sent_at', 'cenceled_at', 'accepted_at', 'expired_at'
    ];
    protected $returnType    = 'App\Entities\Order';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function orders($uid, array $filters)
    {
        $this->builder()->select('
        a.id, a.resi, a.status, a.invoice, a.courier, a.transaction_id,
        b.status as transaction_status
        ')->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left');
        if (!empty($filters['status'])) {
            $this->builder->where([
                'b.user_id' => $uid,
                'b.status' => 'settlement',
                'a.status' => $filters['status']
            ]);
        } else {
            $this->builder->where([
                'b.user_id' => $uid, 
                'b.status' => 'settlement']);
        }
        return $this->builder->groupBy('a.id')->orderBy('a.id', 'DESC')->get()->getResult($this->returnType);
    }

    public function count($uid, string $status = null)
    {
        $this->builder()->select('
        a.status, a.transaction_id,
        b.status as transaction_status, b.user_id,
        ')->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left')
            ->groupBy('a.id');
            if ($status) {
                $this->builder->where([
                    'b.user_id' => $uid,
                    'b.status' => 'settlement',
                    'a.status' => $status
                ]);
            } else {
                $this->builder->where([
                    'b.user_id' => $uid, 
                    'b.status' => 'settlement']);
            }
        $query = $this->builder->get()->getResultArray();
        return count($query);
    }

    public function countNewOrder($storeId) {
        $this->builder()->select('
        a.status, a.transaction_id,
        b.status as transaction_status,
        c.product_id,
        d.store_id,
        ')->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left')
            ->join('order_items c', 'c.order_id=a.id', 'left')
            ->join('products d', 'd.id=c.product_id', 'left')
            ->groupBy('a.id');
        $this->builder->where(['d.store_id' => $storeId, 'a.status' => null, 'b.status' => 'settlement']);
        $query = $this->builder->get()->getResultArray();
        return count($query);
    }

    public function count_perday()
    {

        $date = date('Y-m-d');
        $query = $this->db->table('orders')
            ->select(' a.transaction_id, COUNT(a.id) as order_count, b.created_at')
            ->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left')
            ->groupBy('a.id')
            ->where('LEFT(b.created_at,' . strlen($date) . ')', $date)->get()->getRow();
        return $query == null ? 0 : (int) $query->order_count;
    }

    public function order($id)
    {
        return $this->builder()->select('
        a.*,
        b.status as transaction_status, b.total, b.address, b.payment_id,
        b.created_at, b.payment_at, b.expired_at as transaction-expired_at,
        c.name as payment_name, c.type_name, c.icon as payment_icon, c.fee
        ')->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left')
            ->join('payments c', 'c.id=b.payment_id', 'left')
            ->groupBy('a.id')->where('a.id', $id)->get()->getRow(0, $this->returnType);
    }

    public function updateFromTransaction($transactionId, ?array $set = null)
    {
        return $this->builder()->update($set, ['transaction_id' => $transactionId]);
    }

    public function ordersFromTransaction($transactionId)
    {
        return $this->builder()->select('
        a.*,
        b.status as transaction_status, b.user_id, b.address,
        ')->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left')
            ->groupBy('a.id')->where('a.transaction_id', $transactionId)->get()->getResult($this->returnType);
    }

    public function orderForStore($limit, $offset, $filters)
    {
        return $this->builder()->select('
        a.*, b.user_id, b.created_at,
        SUM(DISTINCT c.price) as subtotal,
        d.store_id,
        e.email, e.avatar,
        f.first_name, f.last_name
        ')->from('orders a')
            ->join('transactions b', "b.id=a.transaction_id and b.status='settlement'")
            ->join('order_items c', 'c.order_id=a.id', 'inner')
            ->join('products d', 'd.id=c.product_id')
            ->join('users e', 'e.id=b.user_id')
            ->join('data_customers f', 'f.user_id=b.user_id')
            ->groupBy('a.id')->limit($limit, $offset)->where('d.store_id', $filters['store'])
            ->get()->getResult($this->returnType);
    }

    public function chart($storeId, $last) {
        $this->builder()->select("a.id, a.accepted_at")->from('orders a')
            ->join('order_items b', 'b.order_id=a.id', 'left')
            ->join('products c', 'c.id=b.product_id', 'left')
            ->groupBy('a.id');
        $this->builder->where([
        'c.store_id' => $storeId, 
        'a.status' => 'done',
        'LEFT(a.accepted_at,' . strlen($last) . ') >=' => $last
        ]);
        return $this->builder->get()->getResultArray();
    }

    public function jobs()
    {
        return $this->builder()->select('
        a.id, a.status, a.expired_at, a.courier,
        a.transaction_id, b.status as transaction_status, b.user_id
        ')->from('orders a')
            ->join('transactions b', 'b.id=a.transaction_id', 'left')
            ->groupBy('a.id')->get()->getResultArray();
    }
}
