<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Pages extends BaseResourceController
{

    protected $modelName = 'App\Models\Pages';
    protected $format    = 'json';

    public function index()
    {
        $limit  = $this->request->getGet('limit') ?: 10;
        $offset = $this->request->getGet('offset') ?: 0;
        $query  = $this->request->getGet('query');
        $sort   = $this->request->getGet('sort');

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
        $data = $this->model->where(['is_activated' => true, is_numeric($id) ? 'id' : 'link' => $id])->getRow();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('Page not found');
        }
    }

    public function create()
    {
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'          => 'required|string|max_length[20]',
            'content'       => 'required|string',
            'link'          => 'required|string',
            'is_activated'  => 'required|in_list[true,false]',
            'thumb'         => 'uploaded[thumb]|mime_in[thumb,image/jpg,image/jpeg,image/png]|max_size[thumb,4096]',
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

        $thumb = $this->request->getFile('thumb');
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
                ->resize(800, 800, true)
                ->save($path . $thumbName);
        }

        \Config\Services::image()
            ->withFile($path . $thumbName)
            ->resize(200, 200, true)
            ->save($path . 'thumbnails/' . $thumbName);

        $data = [
            'name'          => $this->request->getVar('name'),
            'description'   => $this->request->getVar('description') ?: NULL,
            'is_activated'  => $this->request->getGet('is_activated') == 'false' ? false : true,
            'content'       => $this->request->getVar('content'),
            'link'          => $this->request->getVar('link'),
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
            unlink(ROOTPATH . 'public/images/' . $data->thumb);
            unlink(ROOTPATH . 'public/images/thumbnails/' . $data->thumb);
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
