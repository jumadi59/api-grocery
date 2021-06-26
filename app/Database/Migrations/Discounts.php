<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Discounts extends Migration
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
            'store_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'value'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'type'         => [
                'type'           => 'ENUM',
                'constraint'     => ['promo', 'flash_sale'],
                'default'        => 'promo',
            ],
            'target'         => [
                'type'           => 'ENUM',
                'constraint'     => ['variant', 'product',],
                'default'        => 'product',
            ],
            'target_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'stock'         => [
                'type'              => 'INT',
                'constraint'        => 5,
            ],
            'sold'         => [
                'type'              => 'INT',
                'constraint'        => 5,
            ],
            'valid_at'         => [
                'type'              => 'DATETIME'
            ],
            'expired_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('discounts');
    }

    public function down()
    {
        $this->forge->dropTable('discounts');
    }
}
