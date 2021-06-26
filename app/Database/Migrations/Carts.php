<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Carts extends Migration
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
            ],
            'user_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'count'         => [
                'type'              => 'INT',
                'constraint'        => 11,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('carts');
    }

    public function down()
    {
        $this->forge->dropTable('carts');
    }
}
