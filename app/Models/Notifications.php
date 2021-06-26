<?php

namespace App\Models;

use CodeIgniter\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'from', 'to', 'title', 'label', 'message', 'image', 'action',
        'is_read', 'send_date'
    ];
    protected $returnType    = 'App\Entities\Notification';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $skipValidation     = true;

    public function count($userId)
    {
        $query = $this->builder()->select('COUNT(id) as notify_count')->where([
            'to' => $userId,
            'is_read' => 0
        ])->orWhere([
            'to' => 'all',
            'is_read' => 0
        ])->get()->getRow();
        return $query == null ? 0 : (int) $query->notify_count;
    }

    public function notifys($userId, $limit, $offset)
    {
        return $this->builder()->select()
            ->where('to', $userId)->orWhere('to', 'all')
            ->limit($limit, $offset)->get()->getResult($this->returnType);
    }

    public function notify($id)
    {
        return $this->builder()->select()->where('id', $id)->get()->getRow(0, $this->returnType);
    }
}
