<?php
namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Favorites extends BaseResourceController
 {

    protected $modelName = 'App\Models\Favorites';
    protected $format    = 'json';

    public function index() {
        
        $user       = $this->user();
         $limit     = $this->request->getGet('limit');
         $offset    = $this->request->getGet('offset');
         $data      = $this->model->favorites($user->id);
         if (count($data) > 0) {
             return $this->respond($data);
         } else {
             return $this->respondNoContent("Empty data");
         }
    }

    public function create()
    {
        $user       = $this->user();
        $validation =  \Config\Services::validation();
        $validation->setRules([
            'product_id' => 'required|is_not_unique[products.id]',
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
            'product_id' => $this->request->getVar('product_id'),
            'user_id' => $user->id
        ];
        $result = $this->model->insert($data);
        if (is_int($result)) {
            $data = $this->model->favorite($data['product_id'], $user->id);
            return $this->respondCreated([
                'status'    => 200,
                'data'      => $data,
                'message'   =>  'Data create'
            ], 'Data create');
        } else {
            return $this->respond([ 
                'status'    => 406,
                'error'     => $result,
                'message'   =>  'gagal create'
            ], 406);
        }
    }

    public function delete($id = null)
    {
        $user = $this->user();
        $data = $this->model->getWhere(['product_id' => $id, 'user_id' => $user->id])->getRow();
        if ($data) {
            $this->model->delete($data->id);
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