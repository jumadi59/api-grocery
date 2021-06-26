<?php

namespace App\Controllers;

class Stores extends BaseResourceController
{

    protected $modelName = 'App\Models\Stores';
    protected $format    = 'json';

    public function index()
    {
        $limit      = $this->request->getGet('limit');
        $offset     = $this->request->getGet('offset');
        $query      = $this->request->getGet('query');

        $filters = [
            'sort'  => $this->request->getGet('sort'),
            'lat'   => $this->request->getGet('lat'),
            'long'  => $this->request->getGet('long')
        ];

        $data = !is_null($query) ? $this->model->search($query, $limit, $offset) : $this->model->stores($limit, $offset);

        if (count($data) > 0) {
            $resultData['total'] = $this->model->count($query, $filters);
            return $this->respond([
                'data'      => $resultData,
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function show($id = null)
    {
        $data = $this->model->store($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function userStore()
    {
        $user = $this->user();
        $data = $this->model->storeUser($user->id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with uid ');
        }
    }

    public function create()
    {
        $user = $this->user();
        $store = $this->model->getWhere(['user_id' => $user->id])->getResult();
        if (count($store) > 0) {
            return $this->respond([
                'status'   => 203,
                'error'    => null,
                'message'  =>  'Toko anda sudah ada'
            ], 203);
        }

        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'          => 'required|string',
            'icon'          => 'uploaded[icon]|mime_in[icon,image/jpg,image/jpeg,image/png]|max_size[icon,1096]',
            'thumb'         => 'uploaded[thumb]|mime_in[thumb,image/jpg,image/jpeg,image/png]|max_size[thumb,4096]',
            'address_id'    => 'required|is_not_unique[address.id]',
            'description'   => 'required|string'
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

        $icon = $this->request->getFile('icon');
        $iconName = 'icon_store_'.$icon->getRandomName();
        $icon->move(ROOTPATH . 'public/images/', $iconName);

        $thumb = $this->request->getFile('thumb');
        $path = ROOTPATH . 'public/images/';
        $thumbName = 'store_'.$thumb->getRandomName();
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
            'name'          => $this->request->getVar('name'),
            'icon'          => $iconName,
            'thumb'         => $thumbName,
            'user_id'       => $user->id,
            'address_id'    => $this->request->getVar('address_id'),
            'description'   => $this->request->getVar('description')
        ];

        $result = $this->model->insert($data);
        return $this->status_create($result, 'store');
    }

    public function update($id = null)
    {
        $store = $this->store();
        if (is_null($store)) {
            return $this->failUnauthorized();
        }

        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        $result = $this->model->update($id, $input);
        return $this->status_update($id, $result, 'store');
    }
}
