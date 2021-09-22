<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Midtrans;
use App\Libraries\Setting;
use Midtrans\Config;

class Transactions extends BaseResourceController
{

    protected $modelName = 'App\Models\Transactions';
    protected $format    = 'json';


    public function index()
    {
        $admin = $this->admin();
        $user   = $this->user();

        $limit  = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');
        $query = $this->request->getGet('query');

        $filters = [
            'user_id'      => $this->request->getGet('user_id'),
            'status'  => $this->request->getGet('status')
        ];

        if ($admin) {
            $results = $this->model->transactions($limit, $offset, $filters, $query);
            $resultData['total'] = $this->model->count($query, $filters);
            return $this->respond([
                'data'      => $resultData,
                'results'   => $results
            ]);
        } else if ($user) {
            $filters['user_id'] = $user->id;
            $results = $this->model->transactions($limit, $offset, $filters, $query);
            foreach ($results as $value) {
                $status = Midtrans::statusTransaction($value->id);
                $value->setMidtrans($status);
            }
            return $this->respond($results);
        } else {
            return $this->failUnauthorized();
        }
    }

    public function show($id = null)
    {
        $user   = $this->user();
        $admin = $this->admin();
        $data   = $this->model->transaction($id, $admin !== null ? null : $user->id);
        if ($data) {
            $status = Midtrans::statusTransaction($id);
            $data->setMidtrans($status);
            if ($admin === null) {
                $data->description  = "Dicek dalam 5 menit setelah pembayaran berhasil";
                $data->instructions = $this->intructions($data->payment->code);
            }
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function cronJob()
    {
        $orders = $this->model->jobs();
        foreach ($orders as $value) {
            $day = strtotime(date('Y-m-d H:i:s'));
            $expiryDate = strtotime(date($value['expired_at']));
            if ($expiryDate < $day && $value['status'] === 'pending') {
                $this->model->update($value['id'], ['status' => 'expire']);
            }
        }
        return $this->respond(['status' => 200, 'message' => 'ok']);
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


    public function cancel($id = null)
    {
        $user = $this->user();
        $data = $this->model->transaction($id, $user->id);
        if ($data) {
            if ($data->status == 'pending') {
                $transaction = new \Midtrans\Transaction();
                Config::$isProduction = Setting::isProduction();
                Config::$serverKey = Setting::getApiKeyMidtransServer();
                $result = $transaction->cancel($id);
                if ($result == 200) {
                    $this->model->update($id, ['status' => 'cancel']);
                    return $this->respond([
                        'status'   => 200,
                        'data'      => (int) $id,
                        'message' =>  'Success cancel'
                    ]);
                } else {
                    return $this->respond([
                        'status'   => $result,
                        'message' =>  'Error cancel'
                    ], 406);
                }
            }
            return $this->respond([
                'status'   => 406,
                'message' =>  'Error cancel'
            ], 406);
        }
    }
}
