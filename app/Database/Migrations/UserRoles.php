<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserRoles extends Migration
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
            'user_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => TRUE
            ],
            'role_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 8,
                'unsigned'   => TRUE
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addUniqueKey(['id', 'user_id']);
        $this->forge->createTable('user_roles');
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
    }
}
