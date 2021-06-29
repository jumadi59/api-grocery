<?php

namespace App\Controllers;

use App\Libraries\Token;
use CodeIgniter\RESTful\ResourceController;

class Auth extends ResourceController
{

    protected $helpers = ['my_helper'];
    protected $modelName = 'App\Models\Users';
    protected $format    = 'json';

    public function register()
    {
        $validation     =  \Config\Services::validation();
        $email          = $this->request->getPost('email');
        $password       = $this->request->getPost('password');
        $firstName      = $this->request->getPost('first_name');
        $lastName       = $this->request->getPost('last_name');
        $phone       = $this->request->getPost('phone');

        $validation->setRules([
            'first_name'    => 'required|min_length[3]',
            'last_name'     => 'required|min_length[3]',
            //'username'      => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'pass_confirm'  => 'required|matches[password]',
            'password'      => 'required|min_length[8]|max_length[20]'
        ]);
        $validation->withRequest($this->request)->run();

        foreach ($validation->getRules() as $key => $value) {
            if ($validation->hasError($key)) {
                return $this->respond([
                    'status' => 203,
                    'message' => $key . ':' . $validation->getError($key)
                ], 203);
            }
        }

        $username_parts = array_filter(explode(" ", strtolower($firstName)));
        $username_parts = array_slice($username_parts, 0, 2);

        $part1 = (!empty($username_parts[0])) ? substr($username_parts[0], 0, 8) : "";
        $part2 = (!empty($username_parts[1])) ? substr($username_parts[1], 0, 5) : "";
        $part3 = $this->model->count(null, null, ['username' => $part1]);

        $username = $part1 . str_shuffle($part2) . $part3;

        $result = $this->model->register([
            'username'          => $username,
            'email'             => $email,
            'password'          => password_hash($password, PASSWORD_BCRYPT),
            'phone'             => !empty($phone) ? $phone : '',
            'avatar'            => 'profile_user-default.png',
            'verified_email'    => 0,
            'verified_phone'    => 0,
        ], [
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'gender'            => 'none',
        ], 2);

        if ($result) {
            $this->model->update_login($result, [
                'user_id'       => $result,
                'time'          => time(),
                'device_token'  => !empty($deviceToken) ? $deviceToken : '',
                'login'         => 'app'
            ]);
            $entity = $this->createData($result);
            return $this->respondCreated([
                'status'    => 201,
                'error'     => null,
                'data'      => $entity,
                'message'   => 'Data Saved'
            ], 'Data Saved');
        } else {
            return $this->respond([
                'status'        => 406,
                'error'         => $result,
                'message'       => 'Gagal register'
            ], 406);
        }
    }

    public function login()
    {
        $email          = $this->request->getPost('email');
        $password       = $this->request->getPost('password');
        $deviceToken    = $this->request->getPost('device_token');

        $validation =  \Config\Services::validation();

        $validation->setRules([
            'email'     => 'required|valid_email|is_not_unique[users.email]',
            'password'  => 'required|min_length[8]|max_length[20]'
        ]);
        $validation->withRequest($this->request)->run();
        if ($validation->hasError('email')) {
            return $this->respond([
                'status' => 203,
                'message' => 'email:' . $validation->getError('email')
            ], 203);
        } else if ($validation->hasError('password')) {
            return $this->respond([
                'status' => 203,
                'message' => 'password:' . $validation->getError('password')
            ], 203);
        } else {

            $result = $this->model->getWhere(['email' => $email])->getRow();
            if ($result) {
                if (password_verify($password, $result->password)) {
                    $this->model->update_login($result->id, [
                        'user_id'       => $result->id,
                        'time'          => time(),
                        'device_token'  => !empty($deviceToken) ? $deviceToken : '',
                        'login'         => 'app'
                    ]);

                    $entity = $this->createData($result->id);
                    return $this->respond([
                        'status'        => 200,
                        'message'       => 'Berhasil login',
                        'data'          => $entity
                    ], 200);
                } else {
                    return $this->respond([
                        'status'        => 203,
                        'message'       => 'password:Password invalid'
                    ], 203);
                }
            } else {
                return $this->respond([
                    'status'    => 203,
                    'data'      => $email,
                    'message'   => 'email:Email invalid'
                ], 203);
            }
        }
    }

    public function loginWith()
    {
        $name           = $this->request->getPost('name');
        $email          = $this->request->getPost('email');
        $gender         = $this->request->getPost('gender');
        $imageUrl       = $this->request->getPost('photo_url');
        $deviceToken    = $this->request->getPost('device_token');
        $loginWith      = $this->request->getPost('login_with');
        $n              = explode(' ', $name);
        $userCount      = $this->model->count($n[0]);
        $username       = $n[0] . $userCount;
        $firstName      = $n[0];
        unset($n[0]);
        $lastName = count($n) > 1 ? implode(' ', $n) : '';

        $result = $this->model->getWhere(['email' => $email])->getRow();
        if ($result) {
            $result = $this->model->user($result->id, true);
            $dataUpdate = [];
            if (empty($result->first_name) && !empty($firstName)) {
                $dataUpdate['first_name'] = $name;
            }
            if (empty($result->last_name) && !empty($lastName)) {
                $dataUpdate['last_name'] = $name;
            }
            if (empty($result->gender) && isset($gender) && !empty($gender)) {
                $dataUpdate['gender'] = $gender;
            }
            if (isset($imageUrl) && !empty($imageUrl) && $result->avatar == 'profile_user-default.png') {
                $dataUpdate['avatar'] = $this->uploadFromUrl($imageUrl, $result->username);
            }
            if (count($dataUpdate) > 0) {
                $this->model->update($result->id, $dataUpdate);
            }
            $result = $this->model->getWhere(['email' => $email])->getRow();
            if ($result) {
                $this->model->update_login($result->id, [
                    'user_id'       => $result->id,
                    'time'          => time(),
                    'device_token'  => !empty($deviceToken) ? $deviceToken : '',
                    'login'         => $loginWith
                ]);
                $entity = $this->createData($result->id);
                return $this->respond([
                    'status'    => 200,
                    'message'   => 'Berhasil login',
                    'data'      => $entity
                ], 200);
            }
        } else {

            $result = $this->model->register([
                'username'          => $username,
                'email'             => $email,
                'password'          => '',
                'phone'             => '',
                'avatar'            => empty($imageUrl) ? 'profile_user-default.png' : $this->uploadFromUrl($imageUrl, $username),
                'verified_email'    => 0,
                'verified_phone'    => 0,
            ], [
                'first_name'        => $firstName,
                'last_name'         => $lastName,
                'gender'            => !empty($gender) ? strtolower($gender) : 'none',
            ], 2);

            if ($result) {
                $this->model->update_login($result, [
                    'user_id'       => $result,
                    'time'          => time(),
                    'device_token'  => !empty($deviceToken) ? $deviceToken : '',
                    'login'         => 'app'
                ]);
                $entity = $this->createData($result);
                return $this->respondCreated([
                    'status'    => 201,
                    'error'     => null,
                    'data'      => $entity,
                    'message'   => 'Data Saved'
                ], 'Data Saved');
            } else {
                return $this->respond([
                    'status'    => 406,
                    'error'     => $result,
                    'message'   => 'Gagal register'
                ], 406);
            }
        }
    }

    public function logout()
    {
        $decoded = Token::get();
        if ($decoded) {
            $logout = $this->model->logout($decoded->data->id, $decoded->data->login_id);
            return $this->respond([
                'status'    => 200,
                'message'   => 'Berhasil logout',
                'data'      => $logout
            ], 200);
        } else {
            return $this->failForbidden();
        }
    }

    public function forgot()
    {
        $email      = $this->request->getPost('email');
        $validation =  \Config\Services::validation();

        $validation->setRules([
            'email' => 'required|valid_email|is_not_unique[users.email]'
        ]);
        $validation->withRequest($this->request)->run();
        if ($validation->hasError('email')) {
            return $this->respond([
                'status' => 203,
                'message' => 'email:' . $validation->getError('email')
            ], 203);
        }

        $user = $this->model->getWhere(['email' => $email])->getRow();
        if ($user) {
            $verify = $this->verifyEmail($user, [
                'action'    => 'forgot',
                'field'     => ['email' => $email],
            ]);
            if ($verify) {
                return $this->respond([
                    'status'    => 200,
                    'data'      => [
                        'code'          => (int) $verify['code'],
                        'field'         => $email,
                        'expired_at'    => date_create($verify['expired_at'])
                    ],
                    'message' => 'success send to ' . $email
                ]);
            } else {
                return $this->respond([
                    'status'   => 406,
                    'error'    => $verify,
                    'message'   =>  'gagal create'
                ], 406);
            }
        } else {
            return $this->failValidationError();
        }
    }

    public function changePass()
    {
        $decoded = Token::get();
        if ($decoded == null || !isset($decoded->data->action)) {
            return $this->failUnauthorized();
        }

        $password   = $this->request->getPost('password');

        $validation =  \Config\Services::validation();

        $validation->setRules([
            'password'              => 'required|min_length[8]|max_length[20]',
            'confirm_password'      => 'required|matches[password]'
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
            $result = $this->model->update($decoded->id, $input);
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

    public function updateToken()
    {
        $decoded = Token::get();
        if ($decoded) {
            $data = $this->model->user($decoded->data->id);
            if ($data) {
                return $this->respond([
                    'status'    => 200,
                    'message'   => 'Berhasil update token',
                    'data'      => $this->createToken($data)
                ], 200);
            } else {
                return $this->failUnauthorized();
            }
        } else {
            return $this->failUnauthorized();
        }
    }

    private function createToken($data)
    {
        $token = Token::create([
            'id'            => (int) $data->id,
            'username'      => $data->username,
            'email'         => $data->email,
            'phone'         => $data->phone,
            'role'          => $data->role,
            'login_id'      => $data->login_id,
        ]);

        return [
            'id'        => (int) $data->id,
            'token'     => $token['token'],
            'expire_at' => $token['expire_at']
        ];
    }

    private function createData($id)
    {
        $data   = $this->model->user($id, true);
        $data->setLoginId($this->model->last_login($id)->id);
        $token  = $this->createToken($data);
        $data->setExpireAt($token['expire_at']);
        $data->setToken($token['token']);
        unset($data->role);
        return $data;
    }

    private function uploadFromUrl($imageUrl, $username)
    {
        $content = curl_get_file_contents($imageUrl);
        $path = ROOTPATH . 'public/images/';
        $profileName = 'profile_' . strtolower($username) . '-' . time() . '.jpg';
        file_put_contents($path . $profileName, $content);
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
        return $profileName;
    }

    private function verifyEmail($user, $data = [])
    {
        $verify = new \App\Controllers\Verify();
        return $verify->sendVerifyEmail($user, $data, '15 minute');
    }
}
