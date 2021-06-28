<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use Exception;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;
use Midtrans\Transaction;

class Midtrans
{

    public static function notify()
    {
        Config::$isProduction = Setting::isProduction();
        Config::$serverKey = Setting::getApiKeyMidtransServer();
        return self::testLocalhost();
        //return new Notification();
    }

    public static function statusTransaction($id)
    {
        Config::$isProduction = Setting::isProduction();
        Config::$serverKey = Setting::getApiKeyMidtransServer();
        $status = self::testLocalhost();
        //$status = Transaction::status($id);
        return self::status($status);
    }

    public static function checkout($order, $customExpired = null)
    {
        Config::$isProduction = Setting::isProduction();
        Config::$serverKey = Setting::getApiKeyMidtransServer();
        Config::$appendNotifUrl = "https://api.jumadi59.com/notifications/handler";
        Config::$overrideNotifUrl = "https://api.jumadi59.com/notifications/handler";

        $data['payment']             = $order->payment;
        $data['transaction_details'] = array(
            'gross_amount'           => $order->total,
            'order_id'               => $order->id
        );
        if ($customExpired) {
            $time = Time::parse($order->created_at);
            $data['custom_expiry']  = array(
                'order_time'        => $time->toLocalizedString('yyyy-MM-dd hh:mm:ss Z'),
                'expiry_duration'   => $customExpired['expiry_duration'],
                'unit'              => $customExpired['unit']
            );
        }

        $data['customer_details'] = [
            'first_name'    => $order->user->first_name,
            'last_name'     => $order->user->last_name,
            'email'         => $order->user->email,
            'phone'         => $order->user->phone,
        ];

        //$data['item_details'] = self::parse($order->order_items);
        $response = null;
        switch ($data['payment']->type) {
            case 'bank_transfer':
                $response = self::bank_transfer($data);
                break;
            case 'debit':
                $response = self::debit($data);
                break;
            case 'card':
                $response = self::credit_card($data);
                break;
            case 'e-wallet':
                $response = self::e_walet($data);
                break;
            case 'cstore':
                $message = "Bayar pesanan di " . Setting::getAppName();
                $response = self::cstore($data, $message);
                break;
            case 'cardless_credit':
                $response = self::cardless_credit($data);
                break;
            default:
                break;
        }
        if (is_object($response)) {
            return $response;
        } else {
            return $response;
        }
    }

    private static function parse($products)
    {
        $result = array();
        foreach ($products as $value) {
            array_push($result, [
                'id' => $value->product->id,
                'price' => $value->product->price,
                'quantity' => $value->quantity,
                'name' => $value->product->name
            ]);
        }

        return $result;
    }

    public static function bank_transfer($data)
    {
        if (strpos(Config::$serverKey, 'your ') != false) {
            return "Please set your payment server key";
        }

        if ($data['payment']->code == 'echannel') {
            $data['payment_type']           = $data['payment']->code;
            $data['echannel']               = array(
                'bill_info1' => 'Payment For:',
                'bill_info2' => 'Belanja',
            );
        } else {
            $data['payment_type']           = $data['payment']->type;
            $data['bank_transfer']          = array(
                'bank' => $data['payment']->code
            );
        }
        if ($data['payment']->code == 'permata') {
            $data['recipient_name']         = '';
        }
        unset($data['payment']);

        try {
            //$response = CoreApi::charge($data);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return self::testLocalhost();
        //return Midtrans::status($response);
    }

    public static function debit($data) {
        if (strpos(Config::$serverKey, 'your ') != false) {
            return "Please set your payment server key";
        }

        $params = array(
            'payment_type'          => $data['payment']->code,
            'transaction_details'   => $data['transaction_details'],
            'customer_details'      => $data['customer_details'],
            'custom_expiry'         => $data['custom_expiry'],
            $data['payment']->code  => array(
                'description' => 'Pembelian Barang'
            )
        );
        try {
            $response = CoreApi::charge($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Midtrans::status($response);
    }

    public static function credit_card($data)
    {
        if (strpos(Config::$serverKey, 'your ') != false) {
            return "Please set your payment server key";
        }
        $params = array(
            'payment_type'          => $data['payment']->type,
            'transaction_details'   => $data['transaction_details'],
            'credit_card'           => array(
                'token_id'          => $data['token_id'],
                'authentication'    => true
            ),
            'customer_details'      => $data['customer_details'],
        );
        try {
            $response = CoreApi::charge($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Midtrans::status($response);;
    }

    public static function e_walet($data)
    {
        if (strpos(Config::$serverKey, 'your ') != false) {
            return "Please set your payment server key";
        }

        $params = array(
            'payment_type'          => $data['payment']->code,
            'transaction_details'   => $data['transaction_details'],
            'customer_details'      => $data['customer_details'],
            'custom_expiry'         => $data['custom_expiry'],
            $data['payment']->code  => array(
                'enable_callback'   => false,
                'callback_url'      => 'someapps://callback'
            )
        );
        if ($data['payment']->code = 'qris') {
            $params['payment']->code  = array(
                'acquirer'   => 'gopay',
            );
        }
        try {
            $response = CoreApi::charge($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Midtrans::status($response);
    }

    public static function cstore($data, $message)
    {
        if (strpos(Config::$serverKey, 'your ') != false) {
            return "Please set your payment server key";
        }

        $params = array(
            'payment_type'          => $data['payment']->type,
            'transaction_details'   => $data['transaction_details'],
            'customer_details'      => $data['customer_details'],
            'custom_expiry'         => $data['custom_expiry'],
            'cstore'                => [
                "store" => $data['payment']->code,
                "message" => $message,
            ]
        );
        try {
            $response = CoreApi::charge($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Midtrans::status($response);
    }

    public static function cardless_credit($data)
    {
        if (strpos(Config::$serverKey, 'your ') != false) {
            return "Please set your payment server key";
        }

        $params = array(
            'payment_type'          => $data['payment']->code,
            'transaction_details'   => $data['transaction_details'],
            'customer_details'      => $data['customer_details']
        );
        try {
            $response = CoreApi::charge($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return Midtrans::status($response);
    }

    private static function status($response)
    {
        return self::status_payment($response);
    }

    private static function status_payment($response)
    {
        $data = null;
        switch ($response->payment_type) {
            case 'bank_transfer':
                if (isset($response->va_numbers)) {
                    $data['bank']           = $response->va_numbers[0]->bank;
                    $data['va_number']      = $response->va_numbers[0]->va_number;
                }
                if (isset($response->permata_va_number)) {
                    $data['bank']           = 'permata';
                    $data['va_number']      = $response->permata_va_number;
                }
                break;
            case 'echannel':
                $data['bank'] = 'echannel';
                $data['bill_key']           = $response->bill_key;
                $data['biller_code']        = $response->biller_code;
                break;
            case 'gopay':
                $data['qr_code_url']          = $response->actions[0]->url;
                $data['deep_link']            = $response->actions[1]->url;
                break;
            case 'cstore':
                $data['store']              = $response->store;
                $data['payment_code']       = $response->payment_code;
                break;
            case 'credit_card':
                $data['redirect_url']       = $response->redirect_url;
                break;
            default:
                if ($response->payment_type = 'bca_klikpay') {
                    $data['type']           = $response->payment_type;
                    $data['redirect_url']   = $response->redirect_url;
                    $data['redirect_data']  = $response->redirect_data;
                } else {
                    $data['type']           = $response->payment_type;
                    $data['redirect_url']   = $response->redirect_url;
                }
                break;
        }

        return $data;
    }

    public static function createExpiredTime($typePayment)
    {
        switch ($typePayment) {
            case 'bank_transfer':
                $data = array(
                    "expiry_duration" => 1,
                    "unit" => "day"
                );
                break;
            case 'e-wallet':
                $data = array(
                    "expiry_duration" => 10,
                    "unit" => "minute"
                );
                break;
            case 'cstore':
                $data = array(
                    "expiry_duration" => 1,
                    "unit" => "day"
                );
                break;
            case 'cardless_credit':
                $data = array(
                    "expiry_duration" => 1,
                    "unit" => "hour"
                );
                break;
            default:
                $data = null;
                break;
        }
        return $data;
    }

    private static function testLocalhost($data = null)
    {
        return json_decode('{
            "va_numbers": [
              {
                "va_number": "39845713169",
                "bank": "bca"
              }
            ],
            "transaction_time": "2020-12-18 18:59:00",
            "transaction_status": "settlement",
            "transaction_id": "c7787b84-4933-49b0-8032-1171392f8080",
            "status_message": "midtrans payment notification",
            "status_code": "201",
            "signature_key": "b43b3ef3b1430cc56c65ca7d015c42a93a88ff97ff1253aa88be0175385147e279a55b85678d0ed0412664d5fc20b697cf900144224718affd2ddbe86660874d",
            "payment_type": "bank_transfer",
            "payment_amounts": [],
            "order_id": "1",
            "merchant_id": "G128939845",
            "gross_amount": "22000.00",
            "fraud_status": "accept",
            "currency": "IDR"
          }');
    }
}
