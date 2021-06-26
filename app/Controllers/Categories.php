<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Categories extends BaseResourceController
{

    protected $modelName = 'App\Models\Categories';
    protected $format    = 'json';

    public function index()
    {

        $limit = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');
        $query = $this->model->where(['is_activated' => true, 'parent' => NULL]);
        if ($limit && $offset) {
            $query->limit($limit, $offset);
        }
        return $this->respond($query->getResult(), 200);
    }

    public function allSub()
    {

        $query = $this->model->where(['is_activated' => true, 'parent' => NULL])->getResult();
        $data = [];
        foreach($query as $category) {
            $sub = $this->model->where(['is_activated' => true, 'parent' => $category->id])->getResult();
            $data[] = $category;
            $data = array_merge($data, $sub);
        }
        return $this->respond($data, 200);
    }
    
    public function sub($parent = null)
    {
        $ct = $this->model->category($parent);
        if ($parent && !isset($ct->parent)) {
            $data = $this->model->where(['is_activated' => true, 'parent' => $parent])->getResult();
            if (count($data) > 0) {
                return $this->respond($data);
            }
        }
        return $this->failNotFound('No Data Found with id ' . $parent);
    }

    public function child($parent = null)
    {
        $subParent = $this->model->category($parent);
        if ($parent && isset($subParent->parent)) {
            $data = $this->model->where(['is_activated' => true, 'parent' => $parent])->getResult();
            if (count($data) > 0) {
                return $this->respond($data);
            }
        }
        return $this->failNotFound('No Data Found with id ' . $parent);
    }

    public function show($id = null)
    {
        $data = $this->model->category($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function create()
    {
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'          => 'required|string|max_length[16]',
            'is_activated'  => 'required|in_list[true,false]',
            'parent'        => 'numeric',
            'icon'          => 'uploaded[icon]|mime_in[icon,image/jpg,image/jpeg,image/png]|max_size[icon,1096]',
            'thumb'         => 'uploaded[thumb]|mime_in[thumb,image/jpg,image/jpeg,image/png]|max_size[thumb,4096]',
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

        $icon = $this->request->getFile('icon');
        $iconName = $icon->getRandomName();
        $icon->move(ROOTPATH . 'public/icons/', $iconName);

        $thumb = $this->request->getFile('thumb');
        $path = ROOTPATH . 'public/images/';
        $thumbName = 'category_'.$thumb->getRandomName();
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
            'parent'        => $this->request->getVar('parent') ?: NULL,
            'is_activated'  => $this->request->getGet('is_activated') == 'false' ? false : true,
            'icon'          => $iconName,
            'thumb'         => $thumbName,
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
            unlink(ROOTPATH . 'public/icons/' . $data->icon);
            unlink(ROOTPATH . 'public/icons/thumbnails/' . $data->icon);
            unlink(ROOTPATH . 'public/images/' . $data->thumb);
            unlink(ROOTPATH . 'public/images/thumbnails/' . $data->thumb);
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
