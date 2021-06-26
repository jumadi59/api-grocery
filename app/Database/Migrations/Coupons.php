<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Coupons extends Migration
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
                'unsigned'          => TRUE, 
                'null'              => TRUE
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'description'           => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'banner'         => [
                'type'              => 'VARCHAR',
                'constraint'           => 300,
            ],
            'code'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => TRUE
            ],
            'min_transaction'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'value'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'unit'         => [
                'type'           => 'ENUM',
                'constraint'     => ['percentage', 'amount'],
                'default'        => 'percentage',
            ],
            'type'         => [
                'type'           => 'ENUM',
                'constraint'     => ['discount', 'cashback', 'free_shipping'],
                'default'        => 'discount',
            ],
            'target'         => [
                'type'           => 'ENUM',
                'constraint'     => ['variant', 'product', 'category', 'store', 'etalase', 'customer'],
                'null'           => TRUE,
                'default'        => NULL,
            ],
            'target_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
                'unsigned'          => TRUE,
                'null'              => TRUE
            ],
            'platfrom'         => [
                'type'           => 'ENUM',
                'constraint'     => ['all', 'mobile', 'web'],
                'default'        => 'all',
            ],
            'stock'         => [
                'type'              => 'INT',
                'constraint'        => 5,
                'unsigned'          => TRUE,
                'null'              => TRUE
            ],
            'valid_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ],
            'expired_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ]
        ]); 
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('coupons');
    }

    public function down()
    {
        $this->forge->dropTable('coupons');
    }
}
