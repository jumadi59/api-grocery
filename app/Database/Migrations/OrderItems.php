<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrderItems extends Migration
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
            'product_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'variant_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
                'null'           => true,
            ],
            'order_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
            ],
            'thumb'         => [
                'type'              => 'VARCHAR',
                'constraint'           => 128,
            ],
            'quantity'         => [
                'type'              => 'INT',
                'constraint'        => 11,
            ],
            'note'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'price'         => [
                'type'              => 'INT',
                'constraint'        => 11,
            ],
            'discount'         => [
                'type'              => 'INT',
                'constraint'        => 3,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('order_items');
    }

    public function down()
    {
        $this->forge->dropTable('order_items');
    }
}
