<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Entities\Order;

class Trackings extends BaseResourceController
{

    protected $modelName = 'App\Models\Trackings';
    protected $format    = 'json';

    public function index($orderId = null)
    {
        $data = $this->model->trackings($orderId);
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function create($uid = null)
    {
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'order_id'      => 'required|is_not_unique[orders.id]',
            'name'          => 'required|string|max_length[60]',
            'description'   => 'required|string|min_length[20]',
        ]);
        $validation->withRequest($this->request)->run();
        foreach ($validation->getRules() as $key => $value) {
            if ($validation->hasError($key)) {
                return $this->respond([
                    'status'    => 203,
                    'message'   => $validation->getError($key)
                ], 203);
            }
        }

        $data = [
            'order_id'      => $this->request->getVar('order_id'),
            'name'          => $this->request->getVar('name'),
            'description'   => $this->request->getVar('description'),
            'created_at'    => date('Y-m-d H:i:s')
        ];
        $result = $this->model->insert($data);
        return $this->status_create($result, 'find');
    }

    public function update($id = null)
    {
        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        $result = $this->model->update($id, $input);
        return $this->status_update($id, $result, 'find');
    }

    public function delete($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->model->delete($id);
            return $this->respondDeleted([
                'status'    => 200,
                'data'      => (int) $id,
                'message'   => 'Data Deleted'
            ], 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    private function trakings(Order $order)
    {
        $trakings = [];

        $trakings[] = new \App\Entities\Tracking([
            'name'          => '',
            'description'   => '',
            'status'        => isset($order->transaction->payment_at),
            'created_at'    => $order->transaction->payment_at
        ]);
        $trakings[] = new \App\Entities\Tracking([
            'name'          => '',
            'description'   => '',
            'status'        => isset($order->sent_at),
            'created_at'    => $order->sent_at
        ]);
    }
}
