<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class Chats extends Model
{
    protected $table         = 'chats';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'seller_id', 'custommer_id', 'sender', 'message', 'data', 'status', 'time'
    ];
    protected $returnType    = 'App\Entities\Chat';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    public function chatWithCustommer($id, $limit, $offset) {
        return $this->builder()
        ->select('seller_id, custommer_id, MAX(time) as time')
        ->where('custommer_id', $id)
        ->orderBy('time', 'DESC')->groupBy('seller_id')
        ->get($limit, $offset)->getResultArray();
    }

    public function chatWithSaller($id, $limit, $offset) {
        return $this->builder()
        ->select('seller_id, custommer_id, MAX(time) as time')
        ->where('seller_id', $id)
        ->orderBy('time', 'DESC')->groupBy('custommer_id')
        ->get($limit, $offset)->getResultArray();
    }

    public function countNewChat($storeId, $userId, $sender)
    {
        return (int) $this->builder()
        ->select('SUM(status) as count_new_chat')
        ->where(['seller_id' => $storeId, 'custommer_id' => $userId, 'sender' => $sender, 'status' => 1])
        ->where('sender', $sender)
        ->get()->getRow()->count_new_chat;
    }

    public function countAllNewChat($userId, $sender)
    {
        return (int) $this->builder()
        ->select('SUM(status) as count_new_chat')
        ->where(['custommer_id' => $userId, 'sender' => $sender, 'status' => 1])
        ->where('sender', $sender)
        ->get()->getRow()->count_new_chat;
    }

    public function chat($storeId, $userId, $limit, $offset) {
        return $this->builder()->select('a.id, a.message, a.status, a.data, a.time, a.sender,
        b.id as store_id, b.name as store_name, b.icon as store_image,
        c.id as user_id, c.username as user_name, c.avatar as user_image')->from('chats a')
        ->join('stores b', 'b.id=a.seller_id')
        ->join('users c', 'c.id=a.custommer_id')
        ->where(['a.seller_id' => $storeId, 'a.custommer_id' => $userId])->groupBy('a.id')
        ->orderBy('a.time', 'ASC')->get($limit, $offset)->getResult($this->returnType);
    }

    public function message($id) {
        return $this->builder()->select('a.id, a.message, a.status, a.data, a.time, a.sender,
        b.id as store_id, b.name as store_name, b.icon as store_image,
        c.id as user_id, c.username as user_name, c.avatar as user_image')->from('chats a')
        ->join('stores b', 'b.id=a.seller_id')
        ->join('users c', 'c.id=a.custommer_id')
        ->where('a.id', $id)->get()->getRow(0, $this->returnType);
    }

    public function updateReed($storeId, $userId, $sender)
    {
        $this->builder()->update(['status' => 0], ['seller_id' => $storeId, 'custommer_id' => $userId, 'sender' => $sender, 'status' => 1]);
    }

    public function lastMessage($storeId, $userId) {
        return $this->builder()->select()
        ->select('a.id, a.message, a.status, a.data, a.time, a.sender,
        b.id as store_id, b.name as store_name, b.icon as store_image,
        d.last_activity as store_last_activity,
        c.id as user_id, c.username as user_name, c.avatar as user_image, c.last_activity as user_last_activity')->from('chats a')
        ->join('stores b', 'b.id=a.seller_id')
        ->join('users c', 'c.id=a.custommer_id')
        ->join('users d', 'd.id=b.user_id')
        ->where(['a.seller_id' => $storeId, 'a.custommer_id' => $userId])
        ->orderBy('a.time', 'ASC')->get()->getLastRow($this->returnType);
    }

}
