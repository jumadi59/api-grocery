<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Verify extends Migration
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
            'code'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 4,
            ],
            'data'         => [
                'type'              => 'TEXT',
            ],
            'expired_at'         => [
                'type'              => 'DATETIME'
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('verify');
    }

    public function down()
    {
        $this->forge->dropTable('verify');
    }
}
