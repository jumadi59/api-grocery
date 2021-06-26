<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => [
                'type'              => 'INT',
                'constraint'        => 12,
                'unsigned'          => TRUE,
                'auto_increment'    => TRUE
            ],
            'username'          => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'password'          => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'email'             => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'phone'             => [
                'type'              => 'VARCHAR',
                'constraint'        => 18,
            ],
            'avatar'        => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
                'default'           => 'user-default.png',
            ],
            'verified_email'    => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'verified_phone'     => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'last_activity' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
                'null'       => TRUE
            ],
            'created_at'        => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addUniqueKey(['username', 'email', 'phone']);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
