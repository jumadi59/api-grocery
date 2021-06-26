<?php

namespace App\Libraries;

class Setting
{
    private static $model = null;
    private static $setting = null;

    private static function initialize() {
        if (Setting::$model == null || Setting::$setting == null) {
            Setting::$model = new \App\Models\Setting();
            Setting::$setting = Setting::$model->getSetting();
        }
    }

    public static function getApiKeyMidtransServer() {
        Setting::initialize();
        return Setting::$setting->api_key_midtrans_server;
    }

    public static function getApiKeyMidtransClient() {
        Setting::initialize();
        return Setting::$setting->api_key_midtrans_client;
    }

    public static function getApiKeyRajaOngkir() {
        Setting::initialize();
        return Setting::$setting->api_key_rajaongkir;
    }

    public static function isProduction() {
        Setting::initialize();
        return Setting::$setting->is_production;
    }

    public static function getFCMToken() {
        Setting::initialize();
        return Setting::$setting->api_key_fcm;
    }

    public static function getAppName() {
        Setting::initialize();
        return Setting::$setting->app_name;
    }

    public static function getAddress() {
        Setting::initialize();
        return Setting::$setting->address;
    }

    public static function getAppID() {
        Setting::initialize();
        return Setting::$setting->app_id;
    }

    public static function EmailConfig() {
        $config['protocol']     = 'smtp';
        $config['SMTPHost']     = 'jumadi59.com';
        $config['SMTPUser']     = 'noreply@jumadi59.com';
        $config['SMTPPass']     = 'NuXF#u{dTI1Y';
        $config['SMTPPort']     = 465;
        $config['SMTPCrypto']   = 'ssl';
        $config['mailType']     = 'html';
        $config['CRLF']         = '\r\n';
        $config['newline']      = '\r\n';
        return $config;
    }
}