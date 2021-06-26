<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Payments extends BaseResourceController
{

    protected $modelName = 'App\Models\Payments';
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->where(['is_activated' => true])->getResult());
    }

    public function show($id = null)
    {
        $data = $this->model->find($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function create()
    {
    }

    public function update($id = null)
    {
    }

    public function delete($id = null)
    {
    }
}
