<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Trackings extends Migration
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
            'order_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'description'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'status'    => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('trackings');
    }

    public function down()
    {
        $this->forge->dropTable('trackings');
    }
}
