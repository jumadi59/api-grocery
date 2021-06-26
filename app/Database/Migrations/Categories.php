<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Categories extends Migration
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
            'display_name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 12,
                'null'              => TRUE
            ],
            'icon'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 148,
            ],
            'thumb'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
                'null'              => TRUE
            ],
            'parent'         => [
                'type'              => 'INT',
                'constraint'        => 12,
                'unsigned'          => TRUE,
                'null'              => TRUE
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
        $this->forge->createTable('categories');
    }

    public function down()
    {
        $this->forge->dropTable('categories');
    }
}
