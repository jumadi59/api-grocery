<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Products extends Migration
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
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'thumb'         => [
                'type'              => 'VARCHAR',
                'constraint'           => 300,
            ],
            'description'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'min_order'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'price'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'weight'         => [
                'type'              => 'INT',
                'constraint'        => 11,
            ],
            'unit'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 15,
            ],
            'stock'         => [
                'type'              => 'INT',
                'constraint'        => 5,
            ],
            'sold'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'store_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'is_activated'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'is_free_shipping'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
                'default'           => false,
            ],
            'is_cod'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
                'default'           => false,
            ],
            'category_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'rating'         => [
                'type'              => 'FLOAT',
                'constraint'        => 2,
                'default'           => 0,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
