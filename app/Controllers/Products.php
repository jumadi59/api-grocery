<?php

namespace App\Controllers;

use App\Models\Variants;

class Products extends Discover
{

    protected $modelName    = 'App\Models\Products';
    protected $format       = 'json';
    protected $isDiscover   = false;

    public function index()
    {
        switch ($this->request->getGet('get')) {
            case 'flash_sale':
                return $this->flashSale();
                break;
            case 'new':
                return $this->new();
                break;
            case 'sold':
                return $this->sold();
                break;
            case 'recommend':
                return $this->recommend();
                break;
            case 'history':
                return $this->recommend();
                break;
            default:
                break;
        }
        $limit      = $this->request->getGet('limit') ?: 8;
        $offset     = $this->request->getGet('offset') ?: 0;
        $query      = $this->request->getGet('query');
        $filters    = [
            'sort'                      => $this->request->getGet('sort'),
            'parent_category'           => $this->request->getGet('parent_category'),
            'category'                  => $this->request->getGet('category'),
            'store'                     => $this->request->getGet('store'),
            'discount'                  => $this->request->getGet('discount') ? ($this->request->getGet('discount') == 'false' ? false : true) : null,
            'product.is_free_shipping'  => $this->request->getGet('free_shipping') ? ($this->request->getGet('free_shipping') == 'false' ? false : true) : null,
            'product.is_cod'            => $this->request->getGet('cod') ? ($this->request->getGet('cod') == 'false' ? false : true) : null,
            'product.price >='          => $this->request->getGet('min_price'),
            'product.price <='          => $this->request->getGet('max_price'),
            'product.is_activated'      => true,
            'product.rating >='         => $this->request->getGet('rating'),
        ];

        $user = $this->user();
        if (is_object($user)) {
            $userId = $user->id;
        } else {
            $userId = null;
        }
        if (!$filters['parent_category'] && $filters['category']) {
            $categoryModel = new \App\Models\Categories();
            $catagory = $categoryModel->category($filters['category']);
            if ($catagory) {
                if (!isset($catagory->parent)) {
                    $filters['parent_category'] = $catagory->id;
                    unset($filters['category']);
                }
            }
        }

        $data = !is_null($query) ? $this->model->search(trim($query), $limit, $offset, $filters, $userId) : $this->model->products($limit, $offset, $filters, $userId);
        
        $resultData['total_load'] = count($data);
        $resultData['total'] = $this->model->count($query, $filters);
        if (count($data) > 0) {
            return $this->respond([
                'data'      => $resultData,
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function sugestion()
    {
        $query      = $this->request->getGet('query');
        $filters    = [
            'sort'                  => $this->request->getGet('sort'),
            'category'              => $this->request->getGet('category'),
            'store'                 => $this->request->getGet('store'),
            'product.price >='      => $this->request->getGet('min_price'),
            'product.price <='      => $this->request->getGet('max_price'),
            'product.is_activated'  => $this->request->getGet('is_active') == 'false' ? false : true
        ];

        return $this->respond($this->model->sugestion($query, $filters));
    }

    public function count()
    {
        $query      = $this->request->getGet('query') ?: null;
        $filters    = [
            'sort'      => $this->request->getGet('sort'),
            'category'  => $this->request->getGet('category'),
            'min_price' => $this->request->getGet('min_price'),
            'max_price' => $this->request->getGet('max_price'),
            'get'       => $this->request->getGet('get'),
            'where'     => $this->request->getGet('where'),

            'lat'       => $this->request->getGet('lat'),
            'long'      => $this->request->getGet('long'),
            'is_active' => $this->request->getGet('is_active') == 'false' ? false : true
        ];
        return $this->respond([
            'status'   => 200,
            'data'     => $this->model->count($query, $filters),
        ]);
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

}
