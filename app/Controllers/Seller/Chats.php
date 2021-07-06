<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseResourceController;
use App\Libraries\Firebase;
use App\Models\Users;

class Chats extends BaseResourceController
{
    protected $modelName = 'App\Models\Chats';
    protected $format    = 'json';

    public function index()
    {
        $limit      = $this->request->getGet('limit') ?: 8;
        $offset     = $this->request->getGet('offset') ?: 0;
        $store = $this->store();

        $results = $this->model->chatWithSaller($store->id, $limit, $offset);
        if (count($results) > 0) {
            $data = [];
            foreach ($results as $value) {
                $lastChat = $this->model->lastMessage($value['seller_id'], $value['custommer_id']);
                $count = $this->model->countNewChat($value['seller_id'], $value['custommer_id'], 'custommer');
                array_push($data, [
                    'count_new' => $count,
                    'last_chat' => $lastChat
                ]);
            }
            return $this->respond([
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function chat($id)
    {
        $limit      = $this->request->getGet('limit') ?: 8;
        $offset     = $this->request->getGet('offset') ?: 0;
        $store = $this->store();

        $this->model->updateReed($store->id, $id, 'custommer');
        $data = $this->model->chat($store->id, $id, $limit, $offset);
        if (count($data) > 0) {
            return $this->respond([
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function create()
    {
        $store = $this->store();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'send_to'           => 'required|numeric|is_not_unique[users.id]',
            'message'           => 'string',
            'data'              => 'string',
            'time'              => 'required|numeric',
        ]);
        $validation->withRequest($this->request)->run();
        foreach ($validation->getRules() as $key => $value) {
            if ($validation->hasError($key)) {
                return $this->respond([
                    'status' => 203,
                    'message' => $validation->getError($key)
                ], 203);
            }
        }

        $data = [
            'seller_id'             => $store->id,
            'custommer_id'          => $this->request->getVar('send_to'),
            'message'               => $this->request->getVar('message'),
            'data'                  => $this->request->getVar('data'),
            'sender'                => 'seller',
            'status'                => 1,
            'time'                  => time(), //$this->request->getVar('time'),
        ];

        $result = $this->model->insert($data);
        if ($result) {
            $userModel = new Users();
            $user = $userModel->user($data['custommer_id']);
            $lastLogin = $userModel->last_login($user->id);

            if ($lastLogin && isset($lastLogin->device_token)) {
                $this->send($lastLogin->device_token, [
                    'title'     => $store->name,
                    'message'   => $data['message'],
                    'payload'   => [
                        'id'            => $result,
                        'reciver'       => [
                            'id'        => $user->id,
                            'name'      => $user->username,
                            'image'     => $user->avatar,
                        ],
                        'message'       => $data['message'],
                        'sender'        => [
                            'id'        => $store->id,
                            'name'      => $store->name,
                            'image'     => $store->icon,
                        ],
                        'time'      => $data['time'],
                        'status'    => 'sent',
                        'data'      => $data['data']
                    ]
                ]);
            }
        }

        return $this->status_create($result, 'message');
    }

    public function delete($id = null)
    {
        $store = $this->store();
        $data = $this->model->getWhere(['id' => $id, 'seller_id' => $store->id, 'sender' => 'seller'])->getRow();
        if ($data) {
            $this->model->delete($id);
            return $this->respondDeleted([
                'status'   => 200,
                'error'    => null,
                'data'     => (int) $id,
                'message' => 'Data Deleted'
            ], 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found');
        }
    }

    public function send($deviceToken, $data)
    {
        $data['payload']['flag']  = 1;
        
        $notif['title']         = $data['title'];
        $notif['message']       = $data['message'];
        $notif['is_background'] = false;
        $notif['timestamp']     = time();
        $notif['payload'] = $data['payload'];

        if (isset($data['image']) && !empty($data['image'])) :           $notif['image'] = $data['image'];
        endif;
        if (isset($data['action']) && !empty($data['action'])) :         $notif['action'] = $data['action'];
        endif;

        if ($deviceToken) {
            return Firebase::send($deviceToken, $notif);
        }
        return false;
    }
}
