<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Variants extends BaseResourceController
{

    protected $modelName = 'App\Models\Variants';
    protected $format    = 'json';

    public function index($pId = null)
    {

        $productId = $pId == null ? $this->request->getGet('product_id') : $pId;
        if (!$productId) {
            return $this->respond([
                'status'   => 200,
                'error'    => null,
                'message'  =>  'Id User not found'
            ]);
        }

        $data = $this->model->variants($productId);
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

}
