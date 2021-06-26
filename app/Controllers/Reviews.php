<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Reviews extends BaseResourceController
{

    protected $modelName = 'App\Models\Reviews';
    protected $format    = 'json';

    public function index($pid = null)
    {
        $productId = $pid == null ? $this->request->getGet('product_id') : $pid;
        if (!$productId) {
            return $this->respond([
                'status'   => 200,
                'message' =>  'Id product not found'
            ]);
        }

        $limit  = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');
        $data   = $this->model->reviews($limit, $offset, $productId);
        if (count($data) > 0) {
            $resultData['total'] = $this->model->count($productId);
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
        $data = $this->model->review($id);
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function userReviews($id = null)
    {

        $user = $this->user($id, true);
        if (is_null($user)) {
            return $this->failUnauthorized();
        }
        $get    = $this->request->getGet('get');
        $limit  = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');
        $data   = [];
        if ($get == 'history' || empty($get)) {
            $data = $this->model->userReviewOrderItems($limit, $offset, $user->id);
        }
        if ($get == 'wait' || empty($get)) {
            $orderItemModel = new \App\Models\OrderItems();
            $data           = array_merge($data, $orderItemModel->userReviewOrderItems($user->id));
        }
        if (count($data) > 0) {
            $resultData['total'] = count($data);
            return $this->respond([
                'data'      => $resultData,
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function storeProductReviews($sid = null)
    {
        $storeId = $sid == null ? $this->request->getGet('store_id') : $sid;
        if (!$storeId) {
            return $this->failNotFound('No Data Found with id ' . $storeId);
        }

        $limit = $this->request->getGet('limit');
        $offset = $this->request->getGet('offset');
        $data = $this->model->storeProductReviews($storeId, $limit, $offset);
        if (count($data) > 0) {
            $resultData['total'] = $this->model->storeProductReviewCount($storeId);
            return $this->respond([
                'data'      => $resultData,
                'results'   => $data
            ]);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function create()
    {
        $user       = $this->user();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'rating'        => 'required|numeric',
            'order_item_id' => 'required|is_not_unique[order_items.id]',
            'review'        => 'required|string'
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
        $data = [
            'user_id'       => $user->id,
            'order_item_id' => $this->request->getVar('order_item_id'),
            'review'        => $this->request->getVar('review'),
            'rating'        => (int) $this->request->getVar('rating')
        ];
        $result = $this->model->insert($data);
        if ($result) {
            $this->updateRatingStore($data);
        }
        return $this->status_create($result, 'review');
    }

    private function updateRatingStore($data) {
        $productModel = new \App\Models\Products();
        $orderItemModel = new \App\Models\OrderItems();
        $storeModel = new \App\Models\Stores();
        $orderItem = $orderItemModel->order_item($data['order_item_id']);
        $ratingStore = array(
            5 => $productModel->countRatingStore($orderItem->product->store->id, 5),
            4 => $productModel->countRatingStore($orderItem->product->store->id, 4),
            3 => $productModel->countRatingStore($orderItem->product->store->id, 3),
            2 => $productModel->countRatingStore($orderItem->product->store->id, 2),
            1 => $productModel->countRatingStore($orderItem->product->store->id, 1)
            );
        $storeModel->update($orderItem->product->store->id, ['rating' => calcAverageRating($ratingStore)]);
    }

    public function update($id = null)
    {

        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        $result = $this->model->update($id, $input);
        return $this->status_update($id, $result, 'review');
    }

    public function delete($id = null)
    {
        $user = $this->user();
        $data = $this->model->getWhere(['id' => $id, 'user_id' => $user->id])->getRow();
        if ($data) {
            $this->model->delete($id);
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
