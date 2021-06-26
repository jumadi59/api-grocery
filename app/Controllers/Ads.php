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

    public function banner()
    {
        $tag = [
            $this->request->getGet('query') ?: '',
            $this->request->getGet('category') ?: '',
            $this->request->getGet('get') ?: ''
        ];
        $data = $this->model->tag($tag);
        if ($data) {
            $this->model->update($data->id, ['show' => $data->show + 1]);
            return $this->respond($data);
        } else {
            return $this->respondNoContent();
        }
    }

    public function dialog()
    {
        $data = $this->model->where(['is_activated' => true, 'type' => 'dialog'])->getRow();
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound();
        }
    }

    public function slide()
    {
        $action = $this->request->getGet('action');
        $wheres = ['is_activated' => true, 'type' => 'slide'];
        if ($action) {
            $wheres['action'] = $action;
        }
        return $this->respond($this->model->where($wheres)->getResult());
    }

    public function click($id = null)
    {
        $data = $this->model->where(['id' => $id])->getRow();
        if ($data) {
            $this->model->update($data->id, ['click' => $data->click + 1]);
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
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
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'title'         => 'required|string|max_length[20]',
            'description'   => 'required|string|min_length[20]',
            'key'           => 'required|string',
            'tag'           => 'required|string',
            'type'          => 'required|in_list[slide,dialog,banner_text,banner_image]',
            'ads'           => 'required|in_list[product,category,store]',
            'action'        => 'required|string',
            'image'         => 'uploaded[image]|mime_in[image,image/jpg,image/jpeg,image/png]|max_size[image,4096]',
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

        $thumb = $this->request->getFile('image');
        $path = ROOTPATH . 'public/images/';
        $thumbName = 'ads_'.$thumb->getRandomName();
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
            ->resize(400, 400, true)
            ->save($path . 'thumbnails/' . $thumbName);

        $time = new Time();
        $time->modify('+ 7 day');
        $expired = $this->request->getVar('expired_at') ?: $time->toDateTimeString();
        $data = [
            'title'         => $this->request->getVar('title'),
            'key'           => $this->request->getVar('key'),
            'type'          => $this->request->getVar('type'),
            'action'        => $this->request->getVar('action'),
            'tag'           => implode(',', $this->request->getVar('tag[]')),
            'image'         => $thumbName,
            'is_activated'  => $this->request->getGet('is_activated') == 'false' ? false : true,
            'ads'           => $this->request->getVar('ads'),
            'description'   => $this->request->getVar('description'),
            'expired_at'    => $expired,
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
            if (!empty($data->image)) {
                unlink(ROOTPATH . 'public/images/' . $data->image);
                unlink(ROOTPATH . 'public/images/thumbnails/' . $data->image);
            }
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
