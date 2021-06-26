<?php

namespace App\Controllers\Seller;

use App\Controllers\BaseResourceController;

class Coupons extends BaseResourceController
{

    protected $modelName = 'App\Models\Coupons';
    protected $format    = 'json';

    public function index()
    {
        $store = $this->store();
        $limit = $this->request->getGet('limit') ?: 10;
        $offset = $this->request->getGet('offset') ?: 0;
        $query = $this->request->getGet('query');
        $sort = $this->request->getGet('sort');

        $sql = $query ? $this->model->search($query)->where(['store_id' => $store->id]) : $this->model->where(['store_id' => $store->id]);
        $data = $sql->sort($sort)->limit($limit, $offset)->getResult();

        if (count($data) > 0) {
            return $this->respond([
                'total' => $this->model->count($query),
                'results' => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function show($id = null)
    {
        $store = $this->store();
        $data = $this->model->where(['id' => $id, 'store_id' => $store->id])->getRow();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function create()
    {
        $store = $this->store();

        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'              => 'required|string|max_length[16]',
            'min_transaction'   => 'required|numeric',
            'value'             => 'required|numeric',
            'unit'              => 'required|in_list[percentage,amount]',
            'code'              => 'required|max_length[10]',
            'banner'            => 'uploaded[banner]|mime_in[banner,image/jpg,image/jpeg,image/png]|max_size[banner,4096]',
            'type'              => 'required|in_list[discount,cashback,free_shipping]',
            'valid_at'          => 'required|valid_date',
            'expired_at'        => 'required|valid_date',
            'stock'             => 'required|numeric',
            'target'            => 'required|in_list[variant,product,category,store,customer]',
            'target_id'         => 'required|numeric',
            'platfrom'          => 'required|in_list[all,mobile,web]',
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
        $thumb = $this->request->getFile('banner');
        $path = ROOTPATH . 'public/images/';
        $thumbName = 'image_'.$thumb->getRandomName();
        $thumb->move($path, $thumbName);
        $info = \Config\Services::image()
            ->withFile($path . $thumbName)
            ->getFile()
            ->getProperties(true);
        if ($info['width'] > 1000 || $info['height'] > 1000) {
            \Config\Services::image()
                ->withFile($path . $thumbName)
                ->resize(1000, 1000, true)
                ->save($path . $thumbName);
        }

        \Config\Services::image()
            ->withFile($path . $thumbName)
            ->resize(300, 300, true)
            ->save($path . 'thumbnails/' . $thumbName);
        $data = [
            'store_id'          => $store->id,
            'name'              => $this->request->getVar('name'),
            'min_transaction'   => $this->request->getVar('min_transaction'),
            'value'             => $this->request->getVar('value'),
            'unit'              => $this->request->getVar('unit'),
            'code'              => $this->request->getVar('code'),
            'banner'            => $thumbName,
            'type'              => $this->request->getVar('type'),
            'valid_at'          => $this->request->getVar('valid_at'),
            'expired_at'        => $this->request->getVar('expired_at'),
            'stock'             => $this->request->getVar('stock'),
            'target'            => $this->request->getVar('target'),
            'target_id'         => $this->request->getVar('target_id'),
            'platfrom'          => $this->request->getVar('platfrom'),
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
        return $this->status_update($id, $result, 'coupon');
    }

    public function delete($id = null)
    {

        $store = $this->store();
        $data = $this->model->where(['id' => $id, 'store_id' => $store->id])->getRow();
        if ($data) {
            $this->model->delete($id);
            if (!empty($data->banner)) {
                unlink(ROOTPATH . 'public/images/' . $data->banner);
                unlink(ROOTPATH . 'public/images/thumbnails/' . $data->banner);
            }
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
