<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use CodeIgniter\I18n\Time;

class Ads extends BaseResourceController
{

    protected $modelName = 'App\Models\Ads';
    protected $format    = 'json';

    /**
     * List ads hanya bisa di akses olah admin
     */
    public function index()
    {

        $limit = $this->request->getGet('limit') ?: 10;
        $offset = $this->request->getGet('offset') ?: 0;
        $query = $this->request->getGet('query');
        $sort = $this->request->getGet('sort');

        $data = !is_null($query) ? $this->model->search($query)
            ->limit($limit, $offset)->sort($sort)->getResult() : $this->model->findAll($limit, $offset);

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
        $data = $this->model->where(['id' => $id])->getRow();
        if ($data) {
            $this->model->update($data->id, ['click' => $data->click + 1]);
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
            'target'        => 'required|in_list[variant,product]',
            'target_id'     => 'required|numeric',
            'value'         => 'required|numeric',
            'type'          => 'required||in_list[promo,flash_sale]',
            'stock'         => 'required|numeric',
            'valid_at'      => 'required|valid_date',
            'expired_at'     => 'valid_date',
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
            'store_id'        => $store->id,
            'target'          => $this->request->getVar('target'),
            'target_id'       => $this->request->getVar('target_id'),
            'value'           => $this->request->getVar('value'),
            'type'            => $this->request->getVar('type'),
            'stock'           => $this->request->getVar('stock'),
            'valid_at'        => $this->request->getVar('valid_at'),
            'expired_at'      => $this->request->getVar('expired_at')
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
                'status'   => 200,
                'data'     => (int) $id,
                'message' => 'Data Deleted'
            ], 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }
}
