<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Setting extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'app_name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 40,
            ],
            'address'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
            ],
            'commission'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
            ],
            'commission_type'         => [
                'type'           => 'ENUM',
                'constraint'     => ['product', 'order'],
                'default'        => 'product',
            ],

            'api_key_rajaongkir'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 500,
            ],
            'api_key_midtrans_server'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
            ],
            'api_key_midtrans_client'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 500,
            ],
            'api_key_fcm'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 500,
            ],
            'app_id'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 500,
            ],

            'is_sigle_store'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'is_production'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
        ]);
        $this->forge->createTable('setting');
    }

    public function down()
    {
        $this->forge->dropTable('setting');
    }
}
