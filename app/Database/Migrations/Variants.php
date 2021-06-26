<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Variants extends Migration
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
            'product_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'thumb_index'         => [
                'type'              => 'INT',
                'constraint'        => 2,
            ],
            'price'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('variants');
    }

    public function down()
    {
        $this->forge->dropTable('variants');
    }
}
