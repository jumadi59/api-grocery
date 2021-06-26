<?php

namespace App\Models;

use CodeIgniter\Model;

class Transactions extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'address', 'payment_id',
        'code_transct', 'total', 'status', 'payment_at', 'expired_at',
    ];
    protected $returnType    = 'App\Entities\Transaction';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $skipValidation     = false;

    public function transactions($uid, $status = null)
    {
        $this->builder()->select('
        a.*, 
        b.email, .b.phone,
        c.first_name, c.last_name,
        d.code, d.name as payment_name, d.type, d.type_name, d.icon as payment_icon, d.fee
        ')
            ->from('transactions a')
            ->join('users b', 'b.id=a.user_id', 'left')
            ->join('data_customers c', 'c.user_id=a.user_id', 'left')
            ->join('payment d', 'd.id=a.payment_id', 'left')
            ->groupBy('a.id');

        $where['a.user_id'] = $uid;
        if ($status) {
            $where['a.status'] = $status;
        }
        $this->builder->where($where);
        return $this->builder->get()->getResult($this->returnType);
    }

    public function count_perday()
    {

        $date = date('Y-m-d');
        $query = $this->builder()
            ->select('created_at, COUNT(id) as transaction_count')
            ->where('LEFT(created_at,' . strlen($date) . ')', $date)->get()->getRow();
        return $query == null ? 0 : (int) $query->transaction_count;
    }

    public function transaction($id, $userId = null)
    {
        if ($userId) {
            return $this->builder()->select('
            a.total, a.status,
            d.description as payment_description, d.note as payment_note, d.code, d.name as payment_name, d.type, d.type_name, d.icon as payment_icon, d.fee
            ')
                ->from('transactions a')
                ->join('payments d', 'd.id=a.payment_id', 'left')
                ->groupBy('a.id')->where(['a.id' => $id, 'a.user_id' => $userId])->get()->getRow(0, $this->returnType);
        } else {
            return $this->builder()->select('
            a.id, a.total, a.status, a.created_at, a.payment_at, a.expired_at, a.user_id,
            b.description as payment_description, b.note as payment_note, b.code, b.name as payment_name, b.type, b.type_name , b.icon as payment_icon, b.fee,
            c.email, c.phone,
            d.first_name, d.last_name,
            ')
                ->from('transactions a')
                ->join('payments b', 'b.id=a.payment_id', 'left')
                ->join('users c', 'c.id=a.user_id', 'left')
                ->join('data_customers d', 'd.user_id=a.user_id', 'left')
                ->groupBy('a.id')->where('a.id', $id)->get()->getRow(0, $this->returnType);
        }
    }

    public function jobs()
    {
        return $this->builder()->select('
        id, status, expired_at')->get()->getResultArray();
    }

    public function delete($id = null, bool $purge = false)
    {
        
        $orders = $this->db->table('orders')->select('id')->where('transaction_id', $id)->get()->getResultArray();
        foreach ($orders as $value) {
            $this->db->table('order_items')->delete(['order_id' => $value['id']]);
        }
        $this->db->table('orders')->delete(['transaction_id' => $id]);

        return parent::delete($id, $purge);
    }

    public function inserts($data, $orders)
    {
        $this->db->transStart();
        if (!isset($data['address']['id']) || $data['address']['id'] == 0) {
            $data['address']['id'] = $this->db->table('address')->insert($data['address']);
        }
        $data['address'] = json_encode($data['address']);
        $transctId = $this->insert($data);

        foreach ($orders as $order) {
            $order['transaction_id'] = $transctId;
            $carts = $order['carts'];
            unset($order['carts']);
            $this->db->table('orders')->insert($order);
            $orderId = $this->db->insertID();

            foreach ($carts as $item) {
                $item['order_id'] = $orderId;
                $this->db->table('order_items')->insert($item);
            }
        }
        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            return null;
        } else {
            return $transctId;
        }
    }
}
