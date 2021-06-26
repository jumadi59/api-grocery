<?php

namespace App\Entities;

use CodeIgniter\Entity;

class Setting extends Entity
{
    protected $app_name;
    protected $address;
    protected $app_id;

    protected $api_key_rajaongkir;
    protected $api_key_midtrans_server;
    protected $api_key_midtrans_client;
    protected $api_key_fcm;
    protected $is_production;
    protected $is_sigle_store;

    protected $casts = [
        'app_name'                  => 'string',
        'address'                   => 'string',

        'app_id'                    => 'string',
        'api_key_rajaongkir'        => 'string',
        'api_key_midtrans_server'   => 'string',
        'api_key_midtrans_client'   => 'string',
        'api_key_fcm'               => 'string',
        'is_production'             => 'boolean',
        'is_sigle_store'            => 'boolean'
    ];
}
