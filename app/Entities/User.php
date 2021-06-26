<?php

namespace App\Entities;

class User extends BaseEntity
{
    protected $simpleName = 'user';

    protected $id;
    protected $username;
    protected $email;
    protected $phone;
    protected $last_activity;
    protected $first_name;
    protected $last_name;
    protected $avatar;
    protected $gender;
    protected $date_of_birth;

    protected $login_id;
    protected $token;
    protected $role;
    protected $address;

    protected $expire_at;
    protected $device_token;

    protected $verified_email;
    protected $verified_phone;

    protected $created_at;
    protected $updated_at;

    protected $objects = [
        'address' => 'App\Entities\Address'
    ];

    protected $casts = [
        'id'                => 'int',
        'username'          => 'string',
        'email'             => 'string',
        'phone'             => 'string',

        'last_activity'     => 'int',
        'login_id'          => 'int',

        'verified_email'    => 'boolean',
        'verified_phone'    => 'boolean',

        'first_name'        => 'string',
        'last_name'         => 'string',
        'avatar'            => 'string',
        'date_of_birth'     => 'string',
        'gender'            => 'string',

        'role'              => 'int',

        'expire_at'         => 'long',
        'token'             => 'string',
        'address'           => 'object',
        'device_token'      => 'string',
    ];

    public function setPassword(string $pass)
    {
        $this->attributes['password'] = password_hash($pass, PASSWORD_BCRYPT);
        return $this;
    }
    public function setToken(string $token)
    {
        $this->attributes['token'] = $token;
        return $this;
    }
    public function setExpireAt($expired)
    {
        $this->attributes['expire_at'] = $expired;
        return $this;
    }
    public function setLoginId($loginId)
    {
        $this->attributes['login_id'] = $loginId;
        return $this;
    }
}
