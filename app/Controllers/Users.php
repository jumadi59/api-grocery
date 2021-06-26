<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;

class Users extends BaseResourceController
{

    protected $modelName = 'App\Models\Users';
    protected $format    = 'json';

    public function index()
    {
        if ($this->admin()) {
            $limit      = $this->request->getGet('limit');
            $offset     = $this->request->getGet('offset');
            $query      = $this->request->getGet('query');
            $filters    = [
                'sort'      => $this->request->getGet('sort'),
                'address'   => $this->request->getGet('address'),
                'username'  => $this->request->getGet('username')
            ];
            $data = !is_null($query) ? $this->model->search(trim($query), $limit, $offset, $filters) : $this->model->users($limit, $offset, $filters);
            if (count($data) > 0) {
                return $this->respond([
                    'total' => $this->model->count($query, $filters),
                    'results' => $data
                ]);
            } else {
                return $this->respondNoContent("Empty data");
            }
        } else {
            $data = $this->model->user($this->user()->id, TRUE);
            if ($data) {
                return $this->respond($data);
            } else {
                return $this->failNotFound('No Data Found');
            }
        }
    }

    public function show($id = null)
    {
        $user = $this->user($id, true);
        $data = $this->model->user($id, true);
        if ($user) {
            return $this->respond($data);
        } else if ($data) {
            unset_all($data, ['updated_at', 'date_of_birth', 'role', 'user_id' , 'phone']);
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function create()
    {
    }

    public function update($id = null)
    {
        $user = $this->user($id, true);
        if (!$user) {
            return $this->failUnauthorized();
        }
        $input = $this->request->getRawInput();
        if (count($input) == 0) {
            return $this->failValidationError();
        }
        if (isset($input['password'])) {
            unset($input['password']);
        }
        $result = $this->model->update($user->id, $input);
        return $this->status_update($user->id, $result, 'find');
    }

    public function sugestion()
    {
        $user = $this->user();
        $username = $this->request->getGet('username');
        $suggestions = array();
        $result = $this->model->getWhere(['username' => $username])->getResult();
        if (count($result) > 0 && strtolower($username) != strtolower($user->username)) {
            $rand = 10;
            for ($i = 0; $i < $rand; $i++) {
                $part1 = (!empty($user->first_name)) ? substr($user->first_name, 0, 8) : "";
                $part2 = (!empty($user->last_name)) ? substr($user->last_name, 0, 5) : "";
                $part3 = ($rand) ? rand(0, $rand) : "";

                $u = $part1 . str_shuffle($part2) . $part3;
                $r = $this->model->getWhere(['username' => $u])->getRow();
                if (count($suggestions) < 5) {
                    if (!$r) : $suggestions[] = $u;
                    endif;
                }
            }
        }

        return $this->respond([
            'results' => $suggestions
        ]);
    }

    public function avatar()
    {
        $user = $this->user();
        if (!$user) {
            return $this->failUnauthorized();
        }
        $data = $this->model->user($user->id);

        $validation =  \Config\Services::validation();
        $validation->setRules([
            'avatar' => 'uploaded[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]|max_size[avatar,4096]',
        ]);
        $validation->withRequest($this->request)->run();
        if ($validation->hasError('avatar')) {
            return $this->respond([
                'status'    => 203,
                'message'   => $validation->getError('avatar')
            ], 203);
        } else {
            if ($data->avatar != 'user-default.png') {
                unlink(ROOTPATH . 'public/images/' . $data->avatar);
            }
            $profile        = $this->request->getFile('avatar');
            $profileName    = 'profile_'.$profile->getRandomName();
            $path           = ROOTPATH . 'public/images/';
            $profile->move($path, $profileName);
            $info = \Config\Services::image()
                ->withFile($path . $profileName)
                ->getFile()
                ->getProperties(true);

            if ($info['width'] > 1000 || $info['height'] > 1000) {
                \Config\Services::image()
                    ->withFile($path . $profileName)
                    ->resize(1000, 1000, true)
                    ->save($path . $profileName);
            }
            \Config\Services::image()
                ->withFile($path . $profileName)
                ->resize(300, 300, true)
                ->save($path . 'thumbnails/' . $profileName);

            $result = $this->model->update($user->id, ['avatar' => $profileName]);
            return $this->status_update($user->id, $result, 'user');
        }
    }

    public function changePassword()
    {
        $user       = $this->user();
        $password   = $this->request->getPost('password');

        $validation =  \Config\Services::validation();

        $validation->setRules([
            'password'          => 'required|min_length[8]',
            'confirm_password'  => 'required|matches[password]'
        ]);
        $validation->withRequest($this->request)->run();
        if ($validation->hasError('password')) {
            return $this->respond([
                'status'    => 203,
                'message'   => 'password:' . $validation->getError('password')
            ], 203);
        } else if ($validation->hasError('confirm_password')) {
            return $this->respond([
                'status'    => 203,
                'message'   => 'confirm_password:' . $validation->getError('confirm_password')
            ], 203);
        } else {
            $input['password'] = password_hash($password, PASSWORD_BCRYPT);
            $result = $this->model->update($user->id, $input);
            if ($result) {
                return $this->respond([
                    'status'    => 200,
                    'data'      => ['update' => true],
                    'message'   => 'Success change password'
                ]);
            } else {
                return $this->respond([
                    'status'    => 406,
                    'message'   =>  'gagal update'
                ], 406);
            }
        }
    }

    public function changeEmail()
    {
        $user       = $this->user();
        $newEmail   = $this->request->getPost('email');
        $validation =  \Config\Services::validation();

        $validation->setRules([
            'email' => 'required|valid_email|is_unique[users.email]',
        ]);
        $validation->withRequest($this->request)->run();
        if ($validation->hasError('email')) {
            return $this->respond([
                'status'    => 203,
                'message'   => $validation->getError('email')
            ], 203);
        }

        $verify         = new \App\Controllers\Verify();
        $user->email    = $newEmail;
        $result = $verify->sendVerifyEmail($user, [
            'action'    => 'update',
            'table'     => 'users',
            'field'     => ['email' => $newEmail, 'verified_email' => true],
            'where_id'  => $user->id
        ], '15 minute');
        if ($result) {
            return $this->respond([
                'status'    => 200,
                'data'      => [
                    'code'          => (int) $result['code'],
                    'field'         => $newEmail,
                    'expired_at'    => date_create($result['expired_at'])
                ],
                'message' => 'success send to ' . $newEmail
            ]);
        } else {
            return $this->respond([
                'status'    => 406,
                'error'     => $verify,
                'message'   =>  'gagal create'
            ], 406);
        }
    }

    public function changePhone()
    {
        $user       = $this->user();
        $newPhone   = $this->request->getPost('phone');
        $validation =  \Config\Services::validation();

        $validation->setRules([
            'phone' => 'required|is_unique[users.phone]',
        ]);
        $validation->withRequest($this->request)->run();
        if ($validation->hasError('phone')) {
            return $this->respond([
                'status'    => 203,
                'message'   => $validation->getError('phone')
            ], 203);
        }
        $verify = new \App\Controllers\Verify();
        $user->phone = $newPhone;
        $result = $verify->sendVerifyPhone($user, [
            'action'    => 'update',
            'table'     => 'users',
            'field'     => ['phone' => $newPhone, 'verified_phone' => true],
            'where_id'  => $user->id
        ], '15 minute');
        if ($result) {
            return $this->respond([
                'status'    => 200,
                'data'      => [
                    'code'          => (int) $result['code'],
                    'field'         => $newPhone,
                    'expired_at'    => date_create($result['expired_at'])
                ],
                'message' => 'success send to ' . $newPhone
            ]);
        } else {
            return $this->respond([
                'status'    => 406,
                'error'     => $verify,
                'message'   =>  'gagal create'
            ], 406);
        }
    }

    public function delete($id = null)
    {
        $user = $this->user($id, true);
        if (!$user) {
            return $this->failUnauthorized();
        }
        $data = $user == null ? false : $this->model->user($id, true);
        if ($data) {
            if ($data->avatar != 'profile_user-default.png') {
                unlink(ROOTPATH . 'public/images/' . $data->avatar);
                unlink(ROOTPATH . 'public/images/thumbnails/' . $data->avatar);
            }
            $this->model->delete($id);
            return $this->respondDeleted([
                'status'    => 200,
                'data'      => (int) $id,
                'message'   =>  'Data Deleted'
            ], 'Data Deleted');
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }
}
