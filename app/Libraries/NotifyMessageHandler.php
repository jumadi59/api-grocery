<?php

namespace App\Libraries;

use App\Models\Discounts;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Trackings;
use App\Models\Transactions;
use CodeIgniter\I18n\Time;

class NotifyMessageHandler
{

    private $transcationModel;
    private $orderModel;
    private $notif;
    private $trakingModel;
    private $transaction_id;
    public $transcation;
    public $data;
    public $storeUserIds = [];

    public function __construct($data)
    {

        $this->transcationModel = new Transactions();
        $this->orderModel = new Orders();
        $this->trakingModel = new Trackings();
        $this->notif = $data;
        $this->transaction_id = $data->order_id;
        $this->transcation = $this->transcationModel->transaction($this->transaction_id);
        if ($this->transcation->status === 'settlement') {
            return $this;
        }

        $this->data['user']['flag'] = 2;
        $this->data['store']['flag'] = 2;
        switch ($this->notif->transaction_status) {
            case 'settlement':
                $this->data = $this->settlement();
                $this->updateProduct();
                break;
            case 'capture':
                $this->data = $this->capture();
                break;
            case 'pending':
                $this->data = $this->pending();
                break;
            case 'deny':
                $this->data = $this->deny();
                break;
            case 'expire':
                $this->data = $this->expire();
                break;
            case 'cancel':
                $this->data = $this->cancel();
                break;
            default:
                break;
        }
    }


    public function capture()
    {
        $fraud = $this->fraud_status;
        $type = $this->payment_type;
        if ($type == 'credit_card') {
            if ($fraud == 'challenge') {
                $data['user'] = array(
                    'title' => 'Transaction challenged',
                    'message' => "Transaction order_id: " . $this->transaction_id . " is challenged by FDS"
                );
            } else {
                $this->transcationModel->update(
                    $this->transaction_id,
                    ['payment_at' => date('Y-m-d H:i:s'), 'status' => $this->notif->transaction_status]
                );

                $datetime = new Time();
                $datetime->modify('+1 day');
                $this->orderModel->updateFromTransaction(
                    $this->transaction_id,
                    [
                        'expired_at' => $datetime->toDateTimeString(),
                        'status' => 'confirmation'
                    ]
                );
                $this->updateProduct();

                $data['user'] = array(
                    'title' => lang('App.transaction.title_settlement', [
                        'bank_name' => $this->transcation->payment->name,
                        'date' => date('d F Y')
                    ]),
                    'message' => lang('App.transaction.msg_settlement'),
                    'action' => 'transaction',
                    'payload' => [
                        'id' => $this->transaction_id,
                        'status' => 'settlement'
                    ]
                );
                $data['store'] = array(
                    'title' => 'Ada pesanan baru',
                    'message' => "Pesanan ini telah melakukan pembayaran silahkan di prosses"
                );

                return $data;
            }
        }
    }

    public function settlement()
    {
        $this->transcationModel->update(
            $this->transaction_id,
            [
                'payment_at' => date('Y-m-d H:i:s'),
                'status' => $this->notif->transaction_status
            ]
        );
        $datetime = new Time();
        $datetime->modify('+1 day');
        $this->orderModel->updateFromTransaction(
            $this->transaction_id,
            [
                'expired_at' => $datetime->toDateTimeString(),
                'status' => 'confirmation'
            ]
        );

        $data['user'] = array(
            'title' => lang('App.transaction.title_settlement', [
                'bank_name' => $this->transcation->payment->name,
                'date' => date('d F Y')
            ]),
            'message' => lang('App.transaction.msg_settlement'),
            'action' => 'transaction',
            'payload' => [
                'id' => $this->transaction_id,
                'status' => 'settlement'
            ]
        );
        $data['store'] = array(
            'title' => 'Ada pesanan baru',
            'message' => "Pesanan ini telah melakukan pembayaran silahkan di prosses"
        );

        return $data;
    }

    public function pending()
    {
        $this->transcationModel->update(
            $this->transaction_id,
            ['status' => $this->notif->transaction_status]
        );
        $time = new Time($this->transcation->expired_at);
        $time->format('d F Y H:i:s');
        $data['user'] = array(
            'title' => lang('App.transaction.title_pending', ['datetime' => $time->toFormattedDateString()]),
            'message' => lang('App.transaction.msg_pending'),
            'action' => 'transaction',
            'payload' => [
                'id' => $this->transaction_id,
                'status' => 'pending'
            ]
        );
        return $data;
    }

    public function cancel()
    {
        $this->transcationModel->update(
            $this->transaction_id,
            ['status' =>
            $this->notif->transaction_status]
        );
        $data['user'] = array(
            'title' => lang('App.transaction.title_cancel'),
            'message' => lang('App.transaction.msg_cancel'),
            'action' => 'transaction',
            'payload' => [
                'id' => $this->transaction_id,
                'status' => 'cancel'
            ]
        );
        return $data;
    }

    public function deny()
    {
        $this->transcationModel->update(
            $this->transaction_id,
            ['status' => $this->notif->transaction_status]
        );
        $data['user'] = array(
            'title' => lang('App.transaction.title_deny'),
            'message' => lang('App.transaction.msg_deny'),
            'action' => 'transaction',
            'payload' => [
                'id' => $this->transaction_id,
                'status' => 'deny'
            ]
        );
        return $data;
    }

    public function expire()
    {
        $this->transcationModel->update(
            $this->transaction_id,
            ['status' => $this->notif->transaction_status]
        );
        $data['user'] = array(
            'title' => lang('App.transaction.title_expire'),
            'message' => lang('App.transaction.msg_expire'),
            'action' => 'transaction',
            'payload' => [
                'id' => $this->transaction_id,
                'status' => 'expire'
            ]
        );
        return $data;
    }

    public function updateProduct()
    {
        $orderItemModel = new OrderItems();
        $productModel = new Products();
        $discountModel = new Discounts();

        $updateProducts = [];
        $updateDiscounts = [];
        $orders = $this->orderModel->ordersFromTransaction($this->transaction_id);
        foreach ($orders as $value) {
            $items = $orderItemModel->order_items($value->id);
            foreach ($items as $v) {
                $product = $productModel->product($v->product->id);
                if ($product) {
                    $this->storeUserIds[] = $product->store->user->id;
                    array_push($updateProducts, [
                        'id' => $product->id,
                        'stock' => ($product->stock - $v->quantity),
                        'sold' => ($product->sold + $v->quantity)
                    ]);

                    $discount = $discountModel->discountTarget(
                        isset($v->variant_id) ? 'variant' : 'product',
                        isset($v->variant_id) ? $v->variant_id : $v->product->id
                    );
                    if ($discount) {
                        array_push($updateDiscounts, [
                            'id' => $discount->id,
                            'stock' => ($discount->stock - $v->quantity),
                            'sold' => ($discount->sold + $v->quantity)
                        ]);
                    }
                }
            }
        }
        if (count($updateProducts) > 0) {
            $productModel->updateBatch($updateProducts, 'id');
        }
        if (count($updateDiscounts) > 0) {
            $discountModel->updateBatch($updateDiscounts, 'id');
        }
    }
}
