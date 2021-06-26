<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Reviews extends Migration
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
            'order_item_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'user_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'review'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 200,
            ],
            'rating'         => [
                'type'              => 'INT',
                'constraint'        => 5,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('reviews');
    }

    public function down()
    {
        $this->forge->dropTable('reviews');
    }
}
