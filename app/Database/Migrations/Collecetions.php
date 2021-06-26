<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Collesitions extends Migration
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
            'store_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('collecitions');
    }

    public function down()
    {
        $this->forge->dropTable('collecitions');
    }
}
