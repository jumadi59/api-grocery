<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Roles extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('roles');
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
