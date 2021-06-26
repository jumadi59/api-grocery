<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Logins extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 8,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'user_id'            => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'device_token'  => [
                'type'              => 'TEXT',
                'null'              => true,
            ],
            'login' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => TRUE
            ],
            'time' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
                'null'       => TRUE
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addUniqueKey(['id']);
        $this->forge->createTable('logins');
    }

    public function down()
    {
        $this->forge->dropTable('logins');
    }
}
