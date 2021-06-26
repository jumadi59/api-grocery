<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseResourceController;
use App\Models\OrderItems;
use App\Models\Trackings;
use CodeIgniter\I18n\Time;

class Orders extends BaseResourceController
{

    protected $modelName = 'App\Models\Orders';
    protected $format    = 'json';

    public function index()
    {
        $store      = $this->store();
        $orderItems = new OrderItems();
        $data = $this->model->orderForStore($store->id);
        if (count($data) > 0) {
            $resultData['total'] = count($data);
            return $this->respond([
                'data' => $resultData,
                'results' => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function show($id = null)
    {
        $order = $this->model->order($id);
        if ($order) {
            $orderItems = new OrderItems();
            $trakings   = new Trackings();
            $items      = $orderItems->order_items($order->id);
            $order->order_items = $items;
            $order->trackings   = $trakings->trackings($order->id);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
        return $this->respond($order);
    }

    public function chart() {
        
        $store = $this->store();
        $last = $this->request->getGet('lats');
        $time                   = new Time();
        $time->modify('-7 day');
        $results = $this->model->chart($store->id, $time->toDateString());
        $groups = [];
        foreach ($results as  $value) {
            $exp = explode(" ", $value['accepted_at']);
            $groups[$exp[0]] = isset($groups[$exp[0]]) ? ($groups[$exp[0]] + 1) : 1;
        }
        $data = [];
        foreach ($groups as $key => $value) {
            $data[] = ['date' => $key, 'count' => $value];
        }
        return $this->respond($data);
    }


    function status($fun, $id)
    {

        if (function_exists($fun)) {
            return call_user_func($fun, $id);
        } else {
            return $this->respondNoContent();
        }
    }

    public function confirmation($id = null)
    {
        $user = $this->user();
        $store = $this->store($user->id);
        if ($store) {
            $order = $this->model->order($id, $store->id, 'store');
            if ($order) {
                if ($order->transaction->status == 'settlement' && $order->status == '') {
                    $result = $this->model->update($id, ['status' => 'confirmation']);
                    return $this->status_update($id, $result, 'order');
                }
            }
        }
        return $this->respond([
            'status'    => 406,
            'message'   =>  'Error confirmation'
        ], 406);
    }

    public function refuse($id = null)
    {
        $user   = $this->user();
        $store  = $this->store($user->id);
        if ($store) {
            $order = $this->model->order($id, $store->id, 'store');
            if ($order) {
                if ($order->transaction->status == 'settlement' && $order->status == '') {
                    $result = $this->model->update($id, ['status' => 'refuse']);
                    return $this->status_update($id, $result, 'order');
                }
            }
        }
        return $this->respond([
            'status'    => 406,
            'message'   =>  'Error refuse'
        ], 406);
    }

    public function packed($id = null)
    {
        $user   = $this->user();
        $store  = $this->store($user->id);
        if ($store) {
            $order = $this->model->order($id, $store->id, 'store');
            if ($order) {
                if ($order->transaction->status == 'settlement' && $order->status == 'confirmation') {
                    $result = $this->model->update($id, ['status' => 'packed']);
                    return $this->status_update($id, $result, 'order');
                }
            }
        }
        return $this->respond([
            'status'    => 406,
            'message'   =>  'Error packed'
        ], 406);
    }

    public function sent($id = null)
    {
        $user   = $this->user();
        $store  = $this->store($user->id);
        if ($store) {
            $order = $this->model->order($id, $store->id, 'store');
            if ($order) {
                $resi = '';
                if ($order->transaction->status == 'settlement' && $order->status == 'packed') {
                    $result = $this->model->update($id, ['status' => 'sent', 'sent_at' => date('Y-m-d H:i:s'), 'resi' => $resi]);
                    return $this->status_update($id, $result, 'order');
                }
            }
        }
        return $this->respond([
            'status'    => 406,
            'message'   =>  'Error sent'
        ], 406);
    }

    public function taking($id = null)
    {
        $user   = $this->user();
        $store  = $this->store($user->id);
        if ($store) {
            $order = $this->model->order($id, $store->id, 'store');
            if ($order) {
                if ($order->transaction->status == 'settlement' && $order->status == 'sent') {
                    $result = $this->model->update($id, ['status' => 'taking']);
                    return $this->status_update($id, $result, 'order');
                }
            }
        }
        return $this->respond([
            'status'    => 406,
            'message'   =>  'Error taking'
        ], 406);
    }

}
