<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Chats extends Migration
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
            'seller_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'custommer_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'sender'         => [
                'type'           => 'ENUM',
                'constraint'     => ['custommer', 'seller'],
            ],
            'message'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'data'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'status'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'time'         => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => TRUE,
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('chats');
    }

    public function down()
    {
        $this->forge->dropTable('chats');
    }
}
