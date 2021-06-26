<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Setting;
use App\Libraries\Token;
use App\Models\Users;
use CodeIgniter\I18n\Time;

class Verify extends BaseResourceController
{

    protected $modelName = 'App\Models\Verify';
    protected $format    = 'json';


    public function index()
    {
        $code   = $this->request->getGet('code');
        $result = $this->model->getWhere(['code' => $code])->getRow();
        if ($result) {
            $now        = strtotime(date('Y-m-d H:i:s'));
            $expired    = strtotime($result->expired_at);
            if ($expired > $now) {
                $data = json_decode($result->data, true);
                if ($data['action'] == 'forgot') {
                    $userModel = new Users();
                    $user   = $userModel->user($result->user_id);
                    $token  = Token::create([
                        'id'        => $user->id,
                        'action'    => 'forgot',
                        'email'     => $user->email,
                        'phone'     => $user->phone
                    ], (6 * 60 * 60));

                    $this->model->delete($result->id);
                    return $this->respond([
                        'status'    => 200,
                        'data'      => ['token' => $token['token']],
                        'message'   => 'verification success'
                    ], 200);
                } else if ($data['action'] == 'send_code') {
                    $this->model->delete($result->id);
                    return $this->respond([
                        'status'    => 200,
                        'data'      => ['access' => true],
                        'message'   => 'verification success'
                    ], 200);
                } else {
                    $this->model->actionData($result->id, $data);
                    return $this->respond([
                        'status'    => 200,
                        'data'      => ['access' => true],
                        'message'   => 'verification success'
                    ], 200);
                }
            } else {
                $this->model->delete($result->id);
                return $this->respond([
                    'status'    => 203,
                    'message'   => 'Kode OTP Expired' . $expired . '>' . $now
                ], 203);
            }
        } else {
            return $this->respond([
                'status'    => 203,
                'message'   => 'Kode OTP tidak valid'
            ], 203);
        }
    }

    public function send_code()
    {
        $email      = $this->request->getPost('email');
        $phone      = $this->request->getPost('phone');
        $verified   = $this->request->getPost('verified');
        $fields = [];
        if (isset($email) && !empty($email))
            $fields['email'] = $email;
        else if (isset($phone) && !empty($phone))
            $fields['phone'] = $phone;
        else return $this->failValidationError();

        $userModel = new \App\Models\Users();

        $user = $userModel->getWhere($fields)->getRow();
        if ($user) {
            $data = [];
            if ($verified) {
                $fields['verified_' . $verified]  = true;
                $data['field']                  = $fields;
                $data['action']                 = 'update';
                $data['table']                  = 'users';
                $data['where_id']               = $user->id;
            } else {
                $data['field']                  = $fields;
                $data['action']                 = 'send_code';
            }
            if (isset($email) && !empty($email)) {
                $verify = $this->sendVerifyEmail($user, $data);
            } else 
        if (isset($phone) && !empty($phone)) {
                $verify = $this->sendVerifyPhone($user, $data);
            }

            if ($verify) {
                return $this->respond([
                    'status'    => 200,
                    'data'      => [
                        'code'          => (int) $verify['code'],
                        'field'         => empty($email) ? $phone : $email,
                        'expired_at'    => date_create($verify['expired_at'])
                    ],
                    'message' => 'success send to ' . $email
                ]);
            } else {
                return $this->respond([
                    'status'    => 406,
                    'error'     => $verify,
                    'message'   =>  'gagal create'
                ], 406);
            }
        } else {
            return $this->failValidationError();
        }
    }

    public function reset_code()
    {
        $userModel  = new \App\Models\Users();
        $code       = $this->request->getGet('code');
        $result     = $this->model->getWhere(['code' => $code])->getRow();
        if ($result) {
            $this->delete($result->id);
            $user = $userModel->user($result->user_id);
            $data = json_decode($result->data, true);
            if (isset($data['field']['email']) || $data['action'] === 'forgot') {
                if (isset($data['field']['email'])) {
                    $user->email = $data['field']['email'];
                }
                $verify = $this->sendVerifyEmail($user, $data);
            } else {
                $verify = $this->sendVerifyPhone($user, $data);
            }
            if ($verify) {
                return $this->respond([
                    'status'    => 200,
                    'data'      => [
                        'code'          => (int) $verify['code'],
                        'expired_at'    => date_create($verify['expired_at'])
                    ],
                    'message'   => 'success send'
                ]);
            } else {
                return $this->respond([
                    'status'    => 406,
                    'error'     => $verify,
                    'message'   =>  'gagal create'
                ], 406);
            }
        } else {
            return $this->respondNoContent('Kode OTP tidak valid');
        }
    }

    public function cronJob()
    {
        $results    = $this->model->findAll();
        $now        = strtotime(date('Y-m-d H:i:s'));
        foreach ($results as $key => $value) {
            $expired = strtotime($value['expired_at']);
            if ($expired < $now) {
                $this->model->delete($value['id']);
            }
        }
        return $this->respond(['status' => 200, 'message' => "ok"]);
    }

    public function sendVerifyEmail($user, $data, $duration = '10 minute')
    {
        helper(['parser', 'text']);
        $parser     = \Config\Services::parser();
        $email      = \Config\Services::email();

        $email->initialize(Setting::EmailConfig());
        $email->setFrom('noreply@jumadi59.com', Setting::getAppName());
        $email->setTo($user->email);
        $email->setSubject('Your Verification Code');

        $time                   = new Time();
        $time->modify('+' . $duration);
        $expired                = $time->toDateTimeString();
        $insert['expired_at']   = $expired;
        $insert['code']         = random_string('numberic', 4);
        if (!$this->model) {
            $this->model = new \App\Models\Verify();
        }

        $result = $this->model->insert([
            'user_id'       => $user->id,
            'expired_at'    => $insert['expired_at'],
            'code'          => $insert['code'],
            'data'          => json_encode($data)
        ]);
        if ($result) {
            $find = $this->model->find($result);
            $email->setMessage($parser->setData([
                'base_url'      => base_url(),
                'app_name'      => Setting::getAppName(),
                'title'         => 'Your Verification Code',
                'message'       => 'Enter this verification code in field:',
                'msg_valid'     => 'Verification code is valid only for ' . $duration,
                'code_verify'   => $find['code'],
                'address'       => Setting::getAddress(),
            ])->render('templates/verify_code'));
            if ($email->send()) {
                return $find;
            } else {
                $this->model->delete($find['id']);
                return false;
            }
        } else {
            return false;
        }
    }

    public function sendVerifyPhone($user, $data, $duration = '10 minute')
    {
        helper(['parser', 'text']);
        $parser = \Config\Services::parser();
        $email  = \Config\Services::email();

        $email->initialize(Setting::EmailConfig());
        $email->setFrom('noreply@jumadi59.com', Setting::getAppName());
        $email->setTo($user->email);
        $email->setSubject('Your Verification Code');

        $time   = new Time();
        $time->modify('+' . $duration);
        $expired = $time->toDateTimeString();
        $insert['expired_at'] = $expired;
        $insert['code'] = random_string('numberic', 4);
        if (!$this->model) {
            $this->model = new \App\Models\Verify();
        }

        $result = $this->model->insert([
            'user_id'       => $user->id,
            'expired_at'    => $insert['expired_at'],
            'code'          => $insert['code'],
            'data'          => json_encode($data)
        ]);
        if ($result) {
            $find = $this->model->find($result);
            return $find;
        } else {
            return false;
        }
    }
}
