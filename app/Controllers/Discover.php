<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use CodeIgniter\I18n\Time;

class Discover extends BaseResourceController
{

    protected $modelName    = 'App\Models\Products';
    protected $format       = 'json';
    protected $isDiscover   = true;

    private $offset     = 0;
    private $limit      = 10;
    private $filters    = [];
    private $userId;
    private $resultData = [];

    public function index()
    {
        return $this->respondNoContent();
    }

    public function flashSale() {
        $this->getData();

        $discountModel = new \App\Models\Discounts();
        $flashSale = $discountModel->timeFlashSale();
        if (count($flashSale) == 0) {
            return $this->respondNoContent();
        }
        $currentFlashSale = null;
        foreach ($flashSale as $value) {
            if (isValid($value['valid_at'], $value['expired_at'])) {
                $this->filters['discount.valid_at']        = $value['valid_at'];
                $this->filters['discount.expired_at']      = $value['expired_at'];
                $currentFlashSale = $value;
            }
        }

        if (!$currentFlashSale) {
            return $this->respondNoContent();
        }
        $this->discover('flash_sale');
        $this->resultData['expired_at']         = strtotime($currentFlashSale['expired_at']);

        return $this->result();
    }

    public function sold() {
        $this->getData();
        $this->discover('sold');
        return $this->result();
    }

    public function new() {
        $this->getData();

        $time = new Time();
        $time->modify('-7 day');
        $this->filters['product.created_at >=']  = $time->toDateTimeString();
        $this->discover('new');

        return $this->result();
    }

    public function recommend() {
        $this->getData();

        $history = $this->request->getGet('history');
        if (!$history) {
            return $this->respondNoContent("Empty data");
        }
        $this->filters['find'] = explode(',', $history);
        
        return $this->result();
    }

    public function history() {
        $this->getData();

        $history = $this->request->getGet('history');
        if (!$history) {
            return $this->respondNoContent("Empty data");
        }
        $this->filters['find'] = explode(',', $history);
        
        return $this->result();
    }

    private function discover($id) {
        $discoverModel  = new \App\Models\Discover();
        $discover       = $discoverModel->where(['id' => $id])->getRow();
        $discover       = $discover->toArray();
        $this->filters  = array_merge($this->filters, $discover['filters']);
        if ($this->isDiscover) {
            $this->resultData['thumb'] = isset($discover['banner_left']) ? $discover['banner_left']: null;
        } else {
            $this->resultData['thumb'] = isset($discover['banner']) ? $discover['banner']: null;
        }
        unset($discover['filters'], $discover['id'], $discover['banner'], $discover['banner_left']);
        $this->resultData = array_merge($this->resultData, $discover);
    }

    private function getData() {
        $this->limit    = $this->request->getGet('limit') ?: 15;
        $this->offset   = $this->request->getGet('offset') ?: 0;
        $this->filters  = [
            'sort'                  => $this->request->getGet('sort'),
            'category'              => $this->request->getGet('category'),
            'store'                 => $this->request->getGet('store'),
            'product.price >='      => $this->request->getGet('min_price'),
            'product.price <='      => $this->request->getGet('max_price'),
            'product.is_activated'  => true
        ];

        $user = $this->user();
        if (is_object($user)) {
            $this->userId = $user->id;
        } else {
            $this->userId = null;
        }
    }

    private function result() {
        $data = isset($this->filters['find']) ? $this->model->find($this->filters['find']) : $this->model->products($this->limit, $this->offset, $this->filters, $this->userId);

        $this->resultData['total_load'] = count($data);
        $this->resultData['total'] = isset($this->filters['find']) ? count($this->filters['find']) : $this->model->count(null, $this->filters);
        if (count($data) > 0) {
            return $this->respond([
                'data'      => $this->resultData,
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }
}