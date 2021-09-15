<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Midtrans;
use App\Libraries\OrderProgress;
use App\Models\OrderItems;
use App\Models\Trackings;
use App\Models\Transactions;
use CodeIgniter\I18n\Time;

class Orders extends BaseResourceController
{

    protected $modelName = 'App\Models\Orders';
    protected $format    = 'json';

    public function index()
    {
        $user = $this->user();

        $filters = [
            'status' => $this->request->getGet('status'),
        ];
        $orderItems = new OrderItems();
        $data       = $this->model->orders($user->id, $filters);
        
        if ($filters['status'] == 'canceled') {
            $expireds = $this->model->orders($user->id, array('status' => 'expire'));
            if (count($expireds) > 0) $data = array_merge($data, $expireds);
        } else 
        if ($filters['status'] == 'expire') {
            $canceleds = $this->model->orders($user->id, array('status' => 'canceled'));
            if (count($canceleds) > 0) $data = array_merge($data, $canceleds);
        }
        

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $items      = $orderItems->order_items($value->id);
                $data[$key] = $value->setOrderItems($items);
            }
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function show($id = null)
    {
        $order = $this->model->order($id);
        if ($order) {
            $orderItems = new OrderItems();
            $items      = $orderItems->order_items($order->id);
            $order->order_items = $items;
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
        return $this->respond($order);
    }

    public function cronJob()
    {
        $orders = $this->model->jobs();
        foreach ($orders as $value) {
            $day = strtotime(date('Y-m-d H:i:s'));
            $expiryDate = strtotime(date($value['expired_at']));
            if (
                $value['transaction_status'] === 'settlement' &&
                $value['status'] == 'confirmation' &&
                $expiryDate < $day
            ) {
                $this->model->update($value['id'], ['status' => 'expire']);
                $this->setNotify($value['id'], $value['transaction_id']);
            }
        }
        return $this->respond(['status' => 200, 'message' => 'ok']);
    }

    public function create()
    {
        $user = $this->user();
        $transcationModel = new Transactions();
        $orderProggress = new OrderProgress($user, $this->model->count_perday());
        $orderProggress->validation();
        foreach ($orderProggress->errors as $value) {
            return $this->respond([
                'status'    => 203,
                'message'   => $value
            ], 203);
        }

        $data = $orderProggress->getRequest();

        $time = new Time();
        $timeExpired = Midtrans::createExpiredTime($data['payment']->type);
        if ($timeExpired) {
            $time->modify('+' . $timeExpired['expiry_duration'] . ' ' . $timeExpired['unit']);
        } else {
            $time->modify('+1 minute');
        }
        $result = $transcationModel->inserts([
            'user_id'       => $user->id,
            'address'       => $data['address'],
            'payment_id'    => $data['payment']->id,
            'code_transct'  => $data['code_transaction'],
            'total'         => $data['total'],
            'expired_at'    => $time->toDateTimeString()
        ], $data['stores']);

        if ($result) {
            $transaction = $transcationModel->transaction($result);
            $response = Midtrans::checkout($transaction, $timeExpired);
            if ($response) {
                $orderProggress->claerCarts();
                $transaction->setMidtrans($response);
                $transaction->description  = "Dicek dalam 5 menit setelah pembayaran berhasil";
                $transaction->instructions = $this->intructions($transaction->payment->code);
                return $this->respondCreated([
                    'status'    => 201,
                    'data'      => $transaction,
                    'message'   => 'Data Saved'
                ], 'Data Saved');
            } else {
                $transaction->delete($result);
                return $this->respond([
                    'status' => 406,
                    'message' => 'error create order'
                ], 406);
            }
        } else {
            return $this->respond([
                'status' => 406,
                'message' => 'error create order'
            ], 406);
        }
    }

    function status($fun, $id)
    {

        if (function_exists($fun)) {
            return call_user_func($fun, $id);
        } else {
            return $this->respondNoContent();
        }
    }

    public function done($id = null)
    {
        $user   = $this->user();
        $order  = $this->model->order($id, $user->id);
        if ($order) {
            if ($order->transaction->status == 'settlement' && $order->status == 'taking') {
                $result = $this->model->update($id, ['status' => 'done', 'accepted_at' => date('Y-m-d H:i:s')]);
                return $this->status_update($id, $result, 'order');
            }
            return $this->respond([
                'status'    => 406,
                'message'   =>  'Error done'
            ], 406);
        }
    }

    public function canceled($id = null)
    {
        $user   = $this->user();
        $order  = $this->model->order($id, $user->id);
        if ($order) {
            if ($order->transaction->status == 'settlement' && $order->status == 'confirmation') {
                $result = $this->model->update($id, ['status' => 'canceled', 'cenceled_at' => date('Y-m-d H:i:s')]);
                return $this->status_update($id, $result, 'order');
            }
            return $this->respond([
                'status'    => 406,
                'message'   =>  'Error canceled'
            ], 406);
        }
    }


    public function delete($id = null)
    {
        $user   = $this->user();
        $data   = $this->model->getWhere(['id' => $id, 'user_id' => $user->id])->getRow();
        if ($data) {
            $this->model->delete($id);
            $response = [
                'status'    => 200,
                'error'     => null,
                'data'      => (int) $id,
                'message'   => 'Data Deleted'
            ];
            return $this->respondDeleted($response, 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    private function setNotify($orderId, $transactionId, $send = 'all')
    {
        $notification   = new Notifications();
        $transModel     = new \App\Models\Transactions();

        $order = $this->model->order($orderId);
        $transaction = $transModel->transaction($transactionId);
        if ($send == 'all' || $send == 'user') {
            $data = array(
                'title'     => 'Transaction expired',
                'message'   => "Order using ",
                'flag'      => 2
            );

            $notification->sendEmail($transaction->user, $data);
            $notification->send($transaction->user->device_token, $data);
        }
        if ($send == 'all' || $send == 'store') {
            $data = array(
                'title'     => 'Transaction expired',
                'message'   => "Order using ",
                'flag'      => 2
            );

            $notification->sendEmail($transaction->user, $data);
            $notification->send($transaction->user->device_token, $data);
        }
    }
    
    public function intructions($code = null)
    {
        $file = file_get_contents(ROOTPATH . 'public/files/intructions.json');
        $obj = json_decode($file, true);
        $intruction = [];
        foreach ($obj as $key => $value) {
            if ($key == $code) {
                $intruction = $value;
            }
        }

        return $intruction;
    }
}
