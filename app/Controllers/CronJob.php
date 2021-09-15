<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Setting;
use App\Libraries\Token;
use App\Models\Users;
use CodeIgniter\I18n\Time;

class cronJob extends BaseResourceController
{

    protected $format    = 'json';

    public function index() {
        $this->verify();
        $this->transactions();
        $this->orders();
        
        return "";
    }

    public function verify()
    {
        $model      = new \App\Models\Verify();
        $results    = $model->findAll();
        $now        = strtotime(date('Y-m-d H:i:s'));
        $deletes    = [];

        foreach ($results as $key => $value) {
            $expired = strtotime($value['expired_at']);
            if ($expired < $now) {
                array_push($value['id']);
            }
        }

        if (count($deletes) > 0) {
            $model->delete($deletes);
        }
    }

    
    public function transactions()
    {
        $model      = new \App\Models\Transactions();
        $orders     = $model->jobs();
        $updates    = [];

        foreach ($orders as $value) {
            $day = strtotime(date('Y-m-d H:i:s'));
            $expiryDate = strtotime(date($value['expired_at']));
            if ($expiryDate < $day && $value['status'] === 'pending') {
                array_push($updates, ['id' => $value['id'], 'status' => 'expire']);
            }
        }

        if (count($updates) > 0) {
            $model->updateBatch($updates, 'id');
        }
    }

    
    public function orders()
    {
        $model      = new \App\Models\Orders();
        $orders     = $model->jobs();
        
        foreach ($orders as $value) {
            $day = strtotime(date('Y-m-d H:i:s'));
            $expiryDate = strtotime(date($value['expired_at']));
            if (
                $value['transaction_status'] === 'settlement' &&
                $value['status'] == 'confirmation' &&
                $expiryDate < $day
            ) {
                $model->update($value['id'], ['status' => 'expire']);
                $this->setNotify($value['id'], $value['transaction_id']);
            }
        }
    }
    
    private function setNotify($orderId, $transactionId, $send = 'all')
    {
        $notification   = new Notifications();
        $transModel     = new \App\Models\Transactions();

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

            //$notification->sendEmail($transaction->user, $data);
            $notification->send($transaction->user->device_token, $data);
        }
    }
}
