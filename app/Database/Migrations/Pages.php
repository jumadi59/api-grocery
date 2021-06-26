<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pages extends Migration
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
                'constraint'        => 128,
            ],
            'description'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'link'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'content'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('pages');
    }

    public function down()
    {
        $this->forge->dropTable('pages');
    }
}
