<?php

namespace App\Libraries;

use App\Models\Address;
use App\Models\Carts;
use App\Models\CouponClaims;
use App\Models\Payments;
use App\Models\Transactions;
use App\Models\Coupons;
use App\Models\Couriers;
use App\Models\Products;
use App\Models\Variants;

class OrderProgress
{

    private $models = array();
    private $data;
    public $errors = array();
    private $countOrder = 0;
    private $request = array();

    private $user;
    private $carts = array();
    private $stores = array();

    public function __construct($user, $countOrder)
    {
        $this->models = array(
            'transactions'  => new Transactions(),
            'address'       => new Address(),
            'payments'      => new Payments(),
            'coupons'       => new Coupons(),
            'carts'         => new Carts(),
            'products'      => new Products(),
            'variants'      => new Variants(),
            'couriers'      => new Couriers()
        );
        $this->countOrder = $countOrder;
        $this->user = $user;
        $this->data = json_decode(trim(file_get_contents('php://input')), true);
    }

    public function validation()
    {
        if (!$this->data) {
            $this->errors['data'] = 'Bad request';
            return;
        }
        helper('array_helper');
        $validation = ['payment_id', 'stores', 'total', 'stores.*.courier', 'stores.*.carts'];
        foreach ($validation as $key) {
            $data = dot_array_search($key, $this->data);
            if ($data == null) {
                $this->errors[$key] = 'error ' . $key;
            }
        }

        $this->dataPayment();
        $this->dataAddress();
        $this->dataStore();
    }

    public function getRequest()
    {
        $this->request['total'] += $this->request['payment']->fee;
        $this->request['code_transaction'] = $this->createNumber(3, ($this->models['transactions']->count_perday() + 1));
        $this->request['stores'] = $this->stores;
        return $this->request;
    }

    public function dataAddress()
    {
        if (isset($this->data['address'])) {
            $this->data['address']['user_id'] = $this->user->id;
            $this->request['address'] = $this->data['address'];
        } else {
            if (isset($this->data['address_id']) && !empty($this->data['address_id'])) {
                $result = $this->models['address']->detail_address($this->data['address_id']);
                if ($result) : $this->request['address'] = $result->jsonSerialize();
                else : $this->errors['address_id'] = 'Select address_id';
                endif;
            } else {
                $this->errors['address'] = 'Select address';
            }
        }
    }

    public function dataPayment()
    {
        $result = $this->models['payments']->find($this->data['payment_id']);
        if ($result) : $this->request['payment'] = $result;
        else : $this->errors['payment'] = 'Select payment';
        endif;
    }

    public function dataStore()
    {
        $this->request['total'] = 0;
        if (is_array($this->data['stores'])) {
            foreach ($this->data['stores'] as $key => $value) {
                $carts = [];
                $subtotal = 0;
                if (isset($value['carts']) && is_array($value['carts'])) {
                    foreach ($value['carts'] as $v) {
                        $item = isset($v['id']) ? $this->dataCart($v['id']) : $this->dataProduct($v);
                        if (is_array($item)) {
                            $price = $item['price'];
                            $discount = $item['discount'];
                            $subtotal += ($discount == 0 ? $price : ($price - ($price * $discount / 100))) * $item['quantity'];
                            $item['note'] = isset($v['note']) ? $v['note'] : '';
                            array_push($carts, $item);
                            if (isset($v['id'])): array_push($this->carts, $v['id']); endif;
                        } else {
                            $this->errors['cart'] = 'cart not found';
                        }
                    }
                }

                $coupon = $this->dataVoucher($subtotal, $value['id']);
                $courier = $this->dataCourier($value['courier']);

                $this->request['total'] += $courier !== null? ($courier["cost"] + $subtotal) : 0;

                $this->stores[] = [
                    'transaction_id'    => 0,
                    'coupon'            => $coupon !== null? json_encode($coupon) : null,
                    'invoice'           => 'INV' . date('ymd') . $this->createNumber(4, ($this->countOrder + $key)),
                    'resi'              => '',
                    'courier'           => $courier !== null? json_encode($courier) : null,
                    'carts'             => $carts
                ];
            }
        } else {
            $this->errors['stores'] = 'store not found';
        }
    }

    private function dataVoucher($subtotal, $storeId)
    {
        if (isset($this->data['coupon_id'])) {
            $result = $this->models['coupons']->find($this->data['coupon_id']);
            if ($result) {
                if ($result->store_id == $storeId) {
                    if ($subtotal > $result->min_transaction) {
                        $this->request['coupon'] = $result;
                        return $result;
                    } else {
                        $this->errors['coupon'] = 'Coupon error' . $subtotal . '>' . $result->min_transaction;
                    }
                }
            }
        }
        return null;
    }

    private function dataCart($cid)
    {
        $cart = $this->models['carts']->cart($cid);
        if ($cart) {
            return [
                'product_id'    => $cart->product->id,
                'order_id'      => 0,
                'variant_id'    => isset($cart->variant) ? $cart->variant->id : null,
                'name'          => $cart->product->name,
                'thumb'         => $cart->product->thumb,
                'price'         => $cart->product->price,
                'discount'      => isset($cart->product->discount) ? $cart->product->discount->value : 0,
                'quantity'      => $cart->count
            ];
        } else {
            $this->errors['carts'] = 'courier not found';
            return null;
        }
    }

    private function dataProduct($cart)
    {
        $product = $this->models['products']->product($cart['product_id']);
        if ($product) {
            $variant = $this->models['variants']->variant(isset($cart['variant_id']) ? $cart['variant_id'] : null);
            return [
                'product_id'    => $product->id,
                'order_id'      => 0,
                'variant_id'    => isset($variant) ? $variant->id : null,
                'name'          => $product->name,
                'thumb'         => $product->thumb,
                'price'         => isset($variant) ? $variant->price :$product->price,
                'discount'      => isset($variant->discount) ? $variant->discount->value : (isset($product->discount) ? $product->discount->value : 0),
                'quantity'      => (int) 1
            ];
        } else {
            $this->errors['product'] = 'courier not found';
            return null;
        }
    }

    private function dataCourier($cid) {
        $courier = is_int($cid) ? $this->models['couriers']->where($cid)->getRow() : $cid;
        if ($courier) { 
            $courier = is_object($courier) ? get_object_vars($courier) : $courier;
            return [
                "id" => $courier["id"],
                "name" => $courier["name"],
                "icon" => $courier["icon"],
                "cost" => $courier["cost"],
                "description" => $courier["description"],
                "discount" => $courier["discount"],
                "etd" => $courier["etd"],
                "service" => $courier["service"],
            ];
        } else {
            $this->errors['courier'] = 'courier not found';
            return null;
        }
    }

    private function createNumber($max, $num)
    {
        $chars = [];
        for ($i = 0; $i < $max; $i++) {
            array_push($chars, '0');
        }
        $number = implode('', $chars);
        for ($i = 0; $i < $max; $i++) {
            if ((count($chars) - 1) - (strlen($num) - 1) == $i) {
                return substr($number, 0, $i) . $num;
            }
        }
        return '';
    }

    public function claerCarts()
    {
        foreach ($this->carts as $value) {
            $this->models['carts']->delete($value);
        }
        if (isset($this->data['coupon_id'])) {
            $voucherClaimModel = new CouponClaims();
            $voucherClaimModel->update(['coupon_id' => $this->data['coupon_id'], 'user_id' => $this->user->id], ['is_used' => true]);
        }
    }
}
