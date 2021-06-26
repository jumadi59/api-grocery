<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Carts extends BaseResourceController
{

    protected $modelName = 'App\Models\Carts';
    protected $format    = 'json';

    public function index()
    {
        $user   = $this->user();
        $data  = $this->model->carts($user->id);
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function create($uid = null)
    {
        $user = $this->user($uid);

        $validation =  \Config\Services::validation();
        $validation->setRules([
            'product_id'    => 'required|is_not_unique[products.id]',
            'count'         => 'required|numeric',
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
            'product_id'    => $this->request->getVar('product_id'),
            'user_id'       => $user->id,
            'count'         => $this->request->getVar('count'),
            'variant_id'    => $this->request->getVar('variant_id') ?: 0
        ];
        $result = $this->model->insert($data);
        return $this->status_create($result, 'cart');
    }

    public function update($id = null)
    {
        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        $result = $this->model->update($id, $input);
        return $this->status_update($id, $result, 'cart');
    }

    public function delete($id = null)
    {
        $user = $this->user();
        $data = $this->model->getWhere(['id' => $id, 'user_id' => $user->id])->getRow();
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
}
