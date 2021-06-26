<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseResourceController;
use App\Models\Chats;
use App\Models\Orders;

class Notifications extends BaseResourceController
{

    protected $modelName = 'App\Models\Notifications';
    protected $format    = 'json';

    public function index()
    {
        $user   = $this->user();
        $limit  = $this->request->getGet('limit') ?: 20;
        $offset = $this->request->getGet('offset') ?: 0;
        $data   = $this->model->notifys($user->id, $limit, $offset);
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function badges()
    {
        $store           = $this->store();
        $ordersModel    = new Orders();
        $chatModel = new Chats();

        return $this->respond([
            'notify_count'      => $this->model->count($store->id),
            'order_count'       => $ordersModel->countNewOrder($store->id),
            'chat_count'        => $chatModel->countAllNewChat($store->id, 'custommer'),
        ]);
    }

    public function show($id = null)
    {
        $data = $this->model->notify($id);
        if ($data) {
            $user = $this->user($data->to, true);
            if (is_object($user)) {
                $isRead = (bool) $data->is_read;
                if (!$isRead) {
                    $this->model->update($id, ['is_read' => true]);
                }
                return $this->respond($data);
            } else {
                return $this->failUnauthorized();
            }
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            $user = $this->user($data->to);
            if (!is_object($user)) {
                return $this->failUnauthorized();
            }

            if (!empty($data->image)) {
                //unlink(ROOTPATH . 'public/images/' . $data->image);
            }
            $this->model->delete($id);
            $response = [
                'status'    => 200,
                'error'     => null,
                'data'      => (int) $id,
                'message'   =>  'Data Deleted'
            ];
            return $this->respondDeleted($response, 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

}
