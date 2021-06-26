<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Favorites extends Migration
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
            'product_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addUniqueKey('product_id');
        $this->forge->createTable('favorites');
    }

    public function down()
    {
        $this->forge->dropTable('favorites');
    }
}
