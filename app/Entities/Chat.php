<?php namespace App\Entities;

class Chat extends BaseEntity
{
    protected $simpleName = 'chat';

    protected $id;
    protected $sender;
    protected $receiver;
    protected $message;
    protected $data;
    protected $status;
    protected $time;

    protected $casts = [
        'id'                => 'int',
        'sender'            => 'object',
        'receiver'          => 'object',
        'message'           => 'string',
        'data'              => 'string',
        'status'            => 'string',
        'time'              => 'int'
    ];

    public function setAttributes(array $data)
    {
        if (isset($data['status'])) {
            $data['status'] = ((bool)$data['status']) ? 'sent' : 'reed';
        }
        $isSender = $data['sender'] === 'seller';

        if (isset($data['store_id'])) {
            $data[$isSender ? 'sender' : 'receiver'] = [
                'id'            => (int) $data['store_id'],
                'name'          => $data['store_name'],
                'image'         => $data['store_image']
            ];
            if (isset($data['store_last_activity'])) {
                $data[$isSender ? 'sender' : 'receiver']['last_activity'] = (int) $data['store_last_activity'];
            }
        }
        if (isset($data['user_id'])) {
            $data[$isSender ? 'receiver' : 'sender'] = [
                'id'            => (int) $data['user_id'],
                'name'          => $data['user_name'],
                'image'         => $data['user_image'],
            ];
            if (isset($data['user_last_activity'])) {
                $data[$isSender ? 'receiver' : 'sender']['last_activity'] = (int) $data['user_last_activity'];
            }
        }
        return parent::setAttributes($data);
    }
}