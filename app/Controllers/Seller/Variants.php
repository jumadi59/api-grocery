<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseResourceController;

class Variants extends BaseResourceController
{

    protected $modelName = 'App\Models\Variants';
    protected $format    = 'json';

    public function index()
    {

        $store = $this->store();
        $data = $this->model->variantAllFromStore($store->id);
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function fromProduct($pId = null) {

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

    public function show($id = null)
    {
        $store = $this->store();
        $data = $this->model->where(['id' => $id])->getRow();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }


    public function create($pId = null)
    {
        $store = $this->store();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'product_id'    => 'required|is_not_unique[products.id]',
            'name'          => 'required|string|max_length[60]',
            'thumb_index'   => 'required|numeric',
            'price'         => 'required|numeric'
        ]);
        $validation->withRequest($this->request)->run();
        foreach ($validation->getRules() as $key => $value) {
            if ($validation->hasError($key)) {
                return $this->respond([
                    'status' => 203,
                    'message' => $validation->getError($key)
                ], 203);
            }
        }
        $data = [
            'product_id'    => $pId == null ? $this->request->getVar('product_id') : $pId,
            'name'          => $this->request->getVar('name'),
            'thumb_index'   => $this->request->getVar('thumb_index'),
            'price'         => $this->request->getVar('price')
        ];
        $result = $this->model->insert($data);
        return $this->status_create($result, 'variant');
    }

    public function update($id = null)
    {

        $store = $this->store();

        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        $result = $this->model->update($id, $input);
        return $this->status_update($id, $result, 'variant');
    }

    public function delete($id = null)
    {
        $store = $this->store();
        $data = $this->model->find($id);
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
}
