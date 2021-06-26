<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Models\Address;

class Regions extends BaseResourceController
{

    protected $modelName = 'App\Models\Regions';
    protected $format    = 'json';

    public function index()
    {
        $limit  = $this->request->getGet('limit') ?: 10;
        $offset = $this->request->getGet('offset') ?: 0;
        $query  = $this->request->getGet('query');
        if (isset($query)) {
            $result = $this->model->search($query, $limit, $offset);
            if (count($result) > 0) {
                return $this->respond([
                    'results' => $result
                ]);
            } else {
                return $this->respondNoContent("Empty data");
            }
        } else {
            return $this->failValidationError();
        }
    }

    public function provinces()
    {
        return $this->respond($this->model->provinces());
    }

    public function citys($provice = null)
    {
        $result = $this->model->citys($provice);
        if ($result & is_array($result)) {
            return $this->respond($result);
        } else {
            return $this->failNotFound('No Data Found with province ' . $provice);
        }
    }

    public function subdistricts($city = null)
    {
        $result = $this->model->subdistricts($city);
        if ($result & is_array($result)) {
            return $this->respond($result);
        } else {
            return $this->failNotFound('No Data Found with city ' . $city);
        }
    }

    public function address($id = null)
    {
        $addressModel = new Address();
        $address = $addressModel->detail_address($id);
        if ($address) {
            $result = $this->model->getCity($address->city);
            return $this->respond($result);
        } else {
            return $this->failNotFound('No Data Found with address ' . $id);
        }
    }
}
