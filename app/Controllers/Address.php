<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Address extends BaseResourceController
{

    protected $modelName = 'App\Models\Address';
    protected $format    = 'json';

    public function index()
    {
        $user = $this->user();
        $data = $this->model->address($user->id);
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function show($id = null)
    {
        $data = $this->model->detail_address($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function primary()
    {
        $user = $this->user();
        $data = $this->model->address_primary($user->id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent('No Data Found');
        }
    }

    public function create()
    {
        $user = $this->user();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'street'            => 'required|string|max_length[60]',
            'province'          => 'required|string',
            'city'              => 'required|string',
            'subdistrict'       => 'required|string',
            'postal_code'       => 'required|numeric',
            'latitude'          => 'required',
            'longitude'         => 'required',
            'shipping_name'     => 'required',
            'shipping_phone'    => 'required',
            'primary'           => 'required|in_list[true,false]',
            'label'             => 'required|string',
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
            'street'            => $this->request->getVar('street'),
            'province'          => $this->request->getVar('province'),
            'city'              => $this->request->getVar('city'),
            'subdistrict'       => $this->request->getVar('subdistrict'),
            'postal_code'       => $this->request->getVar('postal_code'),
            'latitude'          => $this->request->getVar('latitude'),
            'longitude'         => $this->request->getVar('longitude'),
            'user_id'           => $user->id,
            'shipping_name'     => $this->request->getVar('shipping_name'),
            'shipping_phone'    => $this->request->getVar('shipping_phone'),
            'primary'           => $this->request->getVar('primary') == 'false' ? false : true,
            'label'             => $this->request->getVar('label'),
        ];

        if ($data['primary']) {
            $result = $this->model->address_primary($user->id);
            if ($result) {
                $this->model->update($result->id, ['primary' => false]);
            }
        }
        $result = $this->model->insert($data);
        return $this->status_create($result, 'detail_address');
    }

    public function update($id = null)
    {
        $user = $this->user();
        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        if (isset($input['primary'])) {
            $input['primary'] = $input['primary']  == 'false' ? false : true;
            if ($input['primary']) {
                $data = $this->model->address_primary($user->id);
                $this->model->update($data->id, ['primary' => false]);
            }
        }
        if (isset($input['id'])) {
            unset($input['id']);
        }
        $result = $this->model->update($id, $input);
        return $this->status_update($id, $result, 'detail_address');
    }

    public function delete($id = null)
    {
        $user = $this->user();
        $data = $this->model->getWhere(['id' => $id, 'user_id' => $user->id])->getRow();
        if ($data) {
            $this->model->delete($id);
            return $this->respondDeleted([
                'status'   => 200,
                'error'    => null,
                'data'     => (int) $id,
                'message' => 'Data Deleted'
            ], 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found');
        }
    }
}
