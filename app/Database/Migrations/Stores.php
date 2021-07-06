<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Stores extends Migration
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
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'description'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'icon'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'thumb'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'courier_active'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 150,
            ],
            'is_support_cod'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'address_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'rating'         => [
                'type'              => 'FLOAT',
                'constraint'        => 2,
                'default'           => 0,
            ],
            'is_activated'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('stores');
    }

    public function down()
    {
        $this->forge->dropTable('stores');
    }
}
