<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Firebase;
use App\Libraries\Midtrans;
use App\Libraries\NotifyMessageHandler;
use App\Libraries\Setting;
use App\Models\Carts;
use App\Models\Chats;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Users;

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
        $user           = $this->user();
        $cartModel      = new Carts();
        $ordersModel    = new Orders();
        $orderItemModel = new OrderItems();
        $chatModel = new Chats();

        return $this->respond([
            'cart_count'        => $cartModel->count($user->id),
            'notify_count'      => $this->model->count($user->id),
            'wait_review_count' => $orderItemModel->witReviews($user->id),
            'chat_count'        => $chatModel->countAllNewChat($user->id, 'sellet'),
            'orders'            => [
                'pending_count'         => $ordersModel->count($user->id, 'pending'),
                'confirmation_count'    => $ordersModel->count($user->id, 'confirmation'),
                'packing_count'         => $ordersModel->count($user->id, 'packed'),
                'shipping_count'        => $ordersModel->count($user->id, 'sent'),
                'finish_count'          => $ordersModel->count($user->id, 'done'),
            ]
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

    //access from Midtrans
    public function handler()
    {
        $userModel = new Users();
        $notif = Midtrans::notify();

        $notifyHandler = new NotifyMessageHandler($notif);
        if ($notifyHandler) {
            $transaction = $notifyHandler->transcation;
            $data = $notifyHandler->data;
            $storeUserIds = $notifyHandler->storeUserIds;
            if ($data) {
                if (isset($data['user'])) {
                    $token = $userModel->last_login($transaction->user->id)->device_token;
                    $device['user']['device'] = $this->send($token, $data['user']);
                    $email['user']['email'] = $this->sendEmail($transaction->user, $data['user']);
                }
                if (isset($data['store']) && is_array($storeUserIds)) {
                    foreach ($storeUserIds as $value) {
                        $userStore = $userModel->user($value);
                        $token = $userModel->last_login($userStore->id)->device_token;
                        $device['store']['device'][$value] = $this->send($token, $data['store']);
                        $email['store']['email'][$value] = $this->sendEmail($userStore, $data['store']);
                    }
                }
                return $this->respond([
                    'status'    => 200,
                    'message'   =>  [$device, $email]
                ], 200);
            } else {
                return $this->respond([
                    'status'    => 406,
                    'message'   =>  'error data'
                ], 406);
            }
        }
    }

    public function send($deviceToken, $data)
    {
        
        $notif['title']         = $data['title'];
        $notif['message']       = $data['message'];
        $notif['is_background'] = false;
        $notif['timestamp'] =  (isset($data['timestamp']) && !empty($data['timestamp'])) ? (int) $data['timestamp'] : time();
        if (isset($data['image']) && !empty($data['image'])):       $notif['image'] = $data['image']; endif;
        if (isset($data['action']) && !empty($data['action'])):     $notif['action'] = $data['action']; endif;
        if (isset($data['payload']) && !empty($data['payload'])):   $notif['payload'] = $data['payload']; endif;

        if (is_array($deviceToken)) {
            return Firebase::sendMultiple($deviceToken, $notif);
        } else {
            return Firebase::send($deviceToken, $notif);
        }
    }

    public function sendEmail($user, $data)
    {
        $email = \Config\Services::email();
        $email->initialize(Setting::EmailConfig());
        $email->setFrom('noreply@jumadi59.com', Setting::getAppName());
        $email->setTo($user->email);

        $email->setSubject($data['title']);
        $email->setMessage($data['message']);

        if ($email->send()) {
            return [
                'message' => 'Email berhasil dikirim',
            ];
        } else {
            return [
                'message' => 'Email tidak berhasil dikirim',
            ];
        }
    }

    function testNotification()
    {
        # code...
    }

}
