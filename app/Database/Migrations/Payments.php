<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Payments extends Migration
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
            'code'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'type'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'type_name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'service'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'fee'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'icon'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'description'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'note'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'is_activated'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
