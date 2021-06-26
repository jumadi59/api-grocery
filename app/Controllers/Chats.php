<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Firebase;
use App\Libraries\Setting;
use App\Models\Stores;
use App\Models\Users;

class Chats extends BaseResourceController
{

    protected $modelName = 'App\Models\Chats';
    protected $format    = 'json';

    public function index()
    {
        $limit      = $this->request->getGet('limit') ?: 8;
        $offset     = $this->request->getGet('offset') ?: 0;
        $user = $this->user();
        $results = $this->model->chatWithCustommer($user->id, $limit, $offset);
        if (count($results) > 0) {
            $data = [];
            foreach ($results as $value) {
                $lastChat = $this->model->lastMessage($value['seller_id'], $value['custommer_id']);
                $count = $this->model->countNewChat($value['seller_id'], $value['custommer_id'], 'seller');
                array_push($data, [
                    'id' => $value['seller_id'],
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
        $user = $this->user();
        $this->model->updateReed($id, $user->id, 'seller');
        $data = $this->model->chat($id, $user->id, $limit, $offset);
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
        $user = $this->user();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'send_to'           => 'required|numeric|is_not_unique[stores.id]',
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

        $storeModel = new Stores();
        $store = $storeModel->store($this->request->getVar('send_to'));

        $data = [
            'seller_id'             => $this->request->getVar('send_to'),
            'custommer_id'          => $user->id,
            'message'               => $this->request->getVar('message'),
            'data'                  => $this->request->getVar('data'),
            'sender'                => 'custommer',
            'status'                => 1,
            'time'                  => time(), //$this->request->getVar('time'),
        ];

        $result = $this->model->insert($data);
        if ($result) {
            $userModel = new Users();
            $lastLogin = $userModel->last_login($store->user->id);
            if ($lastLogin && isset($lastLogin->device_token)) {
                $this->send($lastLogin->device_token, [
                    'title'     => $user->username,
                    'message'   => $data['message'],
                    'payload'   => [
                        'id'            => $result,
                        'reciver'       => [
                            'id'        => $data['seller_id'],
                            'name'      => $store->name,
                            'image'     => $store->icon,
                        ],
                        'message'       => $data['message'],
                        'sender'        => [
                            'id'        => $user->id,
                            'name'      => $user->username,
                            'image'     => $user->avatar,
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

    public function reedChat($id = null)
    {
        $user = $this->user();
        $storeModel = new Stores();
        $store = $storeModel->store($id);

        $update = $this->model->updateReed($id, $user->id, 'seller');
        if ($update) {
            $userModel = new Users();
            $lastLogin = $userModel->last_login($store->user->id);
            if ($lastLogin && isset($lastLogin->device_token)) {
                $this->send($lastLogin->device_token, [
                    'title'     => Setting::getAppName(),
                    'message'   => 'Updating...',
                    'payload'   => []
                ]);
            }
        }
    }

    public function delete($id = null)
    {
        $user = $this->user();
        $data = $this->model->getWhere(['id' => $id, 'custommer_id' => $user->id, 'sender' => 'custommer'])->getRow();
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
        $notif['title']         = $data['title'];
        $notif['message']       = $data['message'];
        $notif['is_background'] = false;
        $notif['flag']          = 1;
        $notif['timestamp'] =  (isset($data['timestamp']) && !empty($data['timestamp'])) ? $data['timestamp'] : time();

        if (isset($data['image']) && !empty($data['image'])) :           $notif['image'] = $data['image'];
        endif;
        if (isset($data['action']) && !empty($data['action'])) :         $notif['action'] = $data['action'];
        endif;
        if (isset($data['payload']) && !empty($data['payload'])) :       $notif['payload'] = $data['payload'];
        endif;

        if ($deviceToken) {
            return Firebase::send($deviceToken, $notif);
        }
        return false;
    }

}
