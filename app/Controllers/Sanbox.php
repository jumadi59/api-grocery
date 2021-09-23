<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use CodeIgniter\I18n\Time;

class Sanbox extends BaseResourceController
{

    protected $format    = 'json';

    public function index()
    {
        $productModel = new \App\Models\Products();
        $discountModel = new \App\Models\Discounts();
        $products = $productModel->findAll();

        $updateProducts = [];
        $updateDiscounts = [];
        foreach ($products as $value) {
            $rand = random_int(0, count($products));
            if ($value->stock > 0 && $rand === $value->id) {
                array_push($updateProducts, [
                    'id' => $value->id,
                    'stock' => ($value->stock - 1),
                    'sold' => ($value->sold + 1)
                ]);
                $discount = $discountModel->discountTarget('product', $value->id);
                if ($discount) {
                    if ($discount->stock > 0) {
                        array_push($updateDiscounts, [
                            'id' => $discount->id, 
                            'stock' => ($discount->stock -1), 
                            'sold' => ($discount->sold + 1)
                        ]);
                    }
                }
            } else if ($value->stock === 0 && $rand === $value->id) {
                array_push($updateProducts, [
                    'id' => $value->id,
                    'stock' => random_int(100, 300),
                ]);
            }
        }

        if (count($updateProducts) > 0) {
            $productModel->updateBatch($updateProducts, 'id');
        }
        if (count($updateDiscounts) > 0) {
            $discountModel->updateBatch($updateDiscounts, 'id');
        }
        return $this->respond(['update size' => count($updateProducts)]);
    }

    function updateFlashSale() {
        $time         = new Time();
        $discontModel = new \App\Models\Discounts();
        $discounts    = $discontModel->where(['expired_at <' => $time->toDateTimeString()])->getResult();
        $updates      = [];
        foreach ($discounts as $value) {
            if (isset($value->expired_at)) {
                $time         = new Time();
                $time->modify('+8 hour');
                array_push($updates, [
                    'id' => $value->id,
                    'valid_at' => date('Y-m-d H:i:s'),
                    'expired_at' => $time->toDateTimeString(),
                    'stock' => random_int(10, 100),
                    'sold' => 0,
                    'value' => random_int(2, 60)
                ]);
            }
        }
        if (count($updates) > 0) {
            $discontModel->updateBatch($updates, 'id');
        }
        return $this->respond(['update size' => count($updates)]);
    }

    public function createNewProduct() {
        $productModel = new \App\Models\Products();
        $products = $productModel->findAll();
        $time = new Time();
        $time->modify('-7 day');

        $updateProducts = [];
        foreach ($products as $value) {
            $rand = random_int(0, count($products));
            if ($value->created_at->date < $time->toDateTimeString() && $rand === $value->id) {
                array_push($updateProducts, [
                    'id' => $value->id,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        if (count($updateProducts) > 0) {
            $productModel->updateBatch($updateProducts, 'id');
        }
        return $this->respond(['update size' => count($updateProducts)]);
    }

    function all()
    {
        $this->index();
        $this->updateFlashSale();
    }
}