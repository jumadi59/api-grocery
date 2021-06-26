<?php

namespace App\Controllers;

use App\Libraries\Token;
use App\Models\Stores;
use CodeIgniter\RESTful\ResourceController;

class BaseResourceController extends ResourceController
{

    protected $helpers = ['my_helper'];
    protected $auth = null;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $lang = $this->request->getGet('lang');
        if ($lang) {
            $this->request->setLocale('in');
        }
    }

    private function auth()
    {
        $userModel = new \App\Models\Users();
        $decoded = Token::get();

        if ($decoded) {
            $data = $userModel->user($decoded->data->id, true);
            if ($data) {
                if ($data->role == 2 || $data->role == 1) {
                    $this->auth = $data;
                }
            }
        }
        return $this;
    }

    protected function user($userId = null, $isEqual = false)
    {
        if ($this->auth()->is_access()) {
            if ($isEqual) {
                if (($this->auth->role == 2 || $this->auth->role == 1) && $this->auth->id == $userId) {
                    return $this->auth;
                }
            } else {
                if ($this->auth->role == 2 || $this->auth->role == 1) {
                    return $this->auth;
                }
            }
        }
        return null;
    }

    protected function store()
    {
        if ($this->auth()->is_access()) {
            $storeModel = new Stores();
            return $storeModel->storeUser($this->auth->id);
        } else {
            return null;
        }
    }

    protected function admin()
    {
        if ($this->auth()->is_access()) {
            if ($this->auth->role == 1) {
                return $this->auth;
            }
        }
        return null;
    }

    protected function is_access(): bool
    {
        return is_object($this->auth);
    }

    protected function status_create($result, $fun, array $unset = [])
    {
        if (is_int($result)) {
            $data = $this->model->$fun($result);
            unset_all($data, $unset);
            return $this->respondCreated([
                'status'   => 200,
                'data'     => $data,
                'message' =>  'Data create'
            ], 'Data create');
        } else {
            return $this->respond([
                'status'   => 406,
                'error'    => $result,
                'message' =>  'gagal create'
            ], 406);
        }
    }

    protected function status_update($id, $result, $fun, array $unset = [])
    {
        if ($result) {
            $data = $this->model->$fun($id);
            unset_all($data, $unset);
            return $this->respondUpdated([
                'status'   => 200,
                'data'     => $data,
                'message' =>  'Data Updated'
            ], 'Data Updated');
        } else {
            return $this->respond([
                'status'   => 406,
                'error'    => $result,
                'message' =>  'gagal update'
            ], 406);
        }
    }
}
