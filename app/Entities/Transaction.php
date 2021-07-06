<?php

namespace App\Entities;

class Transaction extends BaseEntity
{
    protected $simpleName = 'transaction';

    protected $id;
    protected $user;
    protected $address;
    protected $payment;
    protected $code_transct;
    protected $total;
    protected $status;
    protected $va_number;
    protected $merchant_id;

    protected $note;
    protected $description;
    protected $instructions;

    protected $created_at;
    protected $updated_at;
    protected $payment_at;
    protected $expired_at;

    protected $objects = [
        'user'      => 'App\Entities\User',
        'payment'   => 'App\Entities\Payment',
        'address'   => 'App\Entities\Address',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'payment_at',
        'expired_at',
    ];

    protected $casts = [
        'id'            => 'int',
        'user'          => 'object',
        'address'       => 'object',
        'payment'       => 'object',
        'code_transct'  => 'string',
        'total'         => 'int',
        'status'        => 'string',
        'va_number'     => 'string',
        'note'          => 'string',
        'description'   => 'string',
        'merchant_id'   => 'string',
        'instructions'  => 'array?object',
    ];


    public function setMidtrans($mitrans)
    {
        foreach ($mitrans as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function setAttributes(array $data)
    {
        if (isset($data['address'])) {
            $data['address'] = json_decode($data['address']);
            unset($data['address']->id, $data['address']->primary);
        }
        return parent::setAttributes($data);
    }
}
