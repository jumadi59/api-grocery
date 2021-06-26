<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Address extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => [
                'type'              => 'INT',
                'constraint'        => 12,
                'unsigned'          => TRUE,
                'auto_increment'    => TRUE
            ],
            'user_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'street'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
            ],
            'primary'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'label'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 20,
            ],
            'province'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'city'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'subdistrict'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'postal_code'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'latitude'         => [
                'type'              => 'DOUBLE',
            ],
            'longitude'         => [
                'type'              => 'DOUBLE',
            ],
            'shipping_name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'shipping_phone'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 18,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('address');
    }

    public function down()
    {
        $this->forge->dropTable('address');
    }
}
