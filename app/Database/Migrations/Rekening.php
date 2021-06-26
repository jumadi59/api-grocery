<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Rekening extends Migration
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
            'bank_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'user_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'no'         => [
                'type'              => 'INT',
                'constraint'        => 15,
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 150,
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('rekening');
    }

    public function down()
    {
        $this->forge->dropTable('rekening');
    }
}
