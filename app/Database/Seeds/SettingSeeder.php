<?php

namespace App\Database\Seeds;

class SettingSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $data = [
            'app_name'  => 'Gocery',
            'app_id' => 'com.jumbox.pasaronline',
            'address' => '444 De Haro Street, Suite 200, San Francisco, CA 94107',
            'api_key_rajaongkir' => 'fe1290152b063cf547ab688ccd213d12',
            'api_key_midtrans_server' => 'SB-Mid-server-K6pijxtgJkzg4eUQSZypIp_-',
            'api_key_midtrans_client' => 'SB-Mid-client-gYcBxX4ul9XnJJkb',
            'api_key_fcm' => 'AAAAP9_opfE:APA91bEaUvfPS6F-N3DdA5jhhCbsuBufSJfHi7qqyCoUrxI6WhRpqBWL5NYxcEUlen9UyHwEOE2kRg8cWNnnexEgNvoxZJa4jQ3Kr5yr-KIC19KyR7cy36Ut1_JzNASK-zILRis7bUx1',
            'is_sigle_store' => false,
            'is_production' => false
        ];
        $this->db->table('setting')->insert($data);
    }
}
