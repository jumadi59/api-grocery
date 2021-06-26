<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notifications extends Migration
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
            'to'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 12,
            ],
            'from'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 12,
            ],
            'label'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'title'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
            ],
            'message'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'image'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
            ],
            'action'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
            ],
            'is_read'         => [
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
        $this->forge->createTable('notifications');
    }

    public function down()
    {
        $this->forge->dropTable('notifications');
    }
}
