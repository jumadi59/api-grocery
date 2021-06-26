<?php

namespace App\Controllers\Seller;

use App\Controllers\Discover;
use App\Models\Variants;

class Products extends Discover
{

    protected $modelName    = 'App\Models\Products';
    protected $format       = 'json';
    protected $isDiscover   = false;

    public function index()
    {
        $store = $this->store();

        $limit  = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');
        $query = $this->request->getGet('query');

        $filters = [
            'sort'      => $this->request->getGet('sort'),
            'category'  => $this->request->getGet('category')
        ];
        $filters['product.store_id'] = $store->id;
        if ($store) {
            $results = $this->model->productStore($limit, $offset, $filters, $query);
            $resultData['total'] = $this->model->count($query, $filters);
            return $this->respond([
                'data'      => $resultData,
                'results'   => $results
            ]);
        }
    }

    public function show($id = null)
    {
        $user       = $this->user();
        $userId     = is_object($user) ? $user->id : null;
        $data       = $this->model->product($id, $userId);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function create()
    {

        $store = $this->store();
        if (is_null($store)) {
            return $this->failUnauthorized();
        }

        $validation =  \Config\Services::validation();
        $validation->setRules([
            'name'              => 'required|string',
            'price'             => 'required|numeric',
            'thumb'             => 'uploaded[thumb]|mime_in[thumb,image/jpg,image/jpeg,image/png]|max_size[thumb,4096]',
            'description'       => 'required|string|min_length[20]',
            'min_order'         => 'required|numeric',
            'category_id'       => 'required|numeric|is_not_unique[categories.id]',
            'unit'              => 'required|string',
            'stock'             => 'required|numeric',
            'weight'            => 'required|numeric',
            'is_activated'      => 'required|in_list[true,false]',
            'is_free_shipping'  => 'required|in_list[true,false]',
            'is_cod'            => 'required|in_list[true,false]',
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

        $thumbs     = $this->request->getFiles();
        $nameThumb  = [];
        foreach ($thumbs['thumb'] as $thumb) {
            $path       = ROOTPATH . 'public/images/';
            $thumbName  = 'product_'.$thumb->getRandomName();

            array_push($nameThumb, $thumbName);
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
        }

        $data = [
            'name'              => $this->request->getVar('name'),
            'price'             => $this->request->getVar('price'),
            'thumb'             => implode(',', $nameThumb),
            'description'       => $this->request->getVar('description'),
            'min_order'         => $this->request->getVar('min_order'),
            'store_id'          => $store->id,
            'category_id'       => $this->request->getVar('category_id'),
            'unit'              => $this->request->getVar('unit'),
            'stock'             => $this->request->getVar('stock'),
            'weight'            => $this->request->getVar('weight') ?: 0,
            'is_activated'      => $this->request->getVar('is_activated') == 'false' ? false : true,
            'is_free_shipping'  => $this->request->getVar('is_free_shipping') == 'false' ? false : true,
            'is_cod'            => $this->request->getVar('is_cod') == 'false' ? false : true,

        ];
        $result = $this->model->insert($data);
        return $this->status_create($result, 'product');
    }

    public function update($id = null)
    {

        $store = $this->store();
        if (is_null($store)) {
            return $this->failUnauthorized();
        }
        $data = $this->model->getWhere(['id' => $id, 'store_id' => $store->id])->getRow();
        if ($data) {
            $input = $this->request->getRawInput();
            if (count($input) == 0) {
                return $this->failValidationError();
            }
            $result = $this->model->update($id, $input);
            return $this->status_update($id, $result, 'product');
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }

    }

    public function delete($id = null)
    {
        $variant = new Variants();

        $store = $this->store();
        if (is_null($store)) {
            return $this->failUnauthorized();
        }

        $data = $this->model->getWhere(['id' => $id, 'store_id' => $store->id])->getRow();
        if ($data) {
            $thumbs = explode(',', $data->thumb);

            foreach ($thumbs as $value) {
                if (!empty($value)) {
                    unlink(ROOTPATH . 'public/images/' . $value);
                    unlink(ROOTPATH . 'public/images/thumbnails/' . $value);
                }
            }
            $this->model->delete($id);
            $variant->deleteFromProduct($id);
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
