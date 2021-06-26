<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Ads extends Migration
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
            'type'         => [
                'type'           => 'ENUM',
                'constraint'     => ['slide', 'dialog', 'top'],
                'default'        => 'slide',
            ],
            'ads'         => [
                'type'           => 'ENUM',
                'constraint'     => ['product', 'store', 'coupon'
                ],
                'default'        => 'product',
            ],
            'image'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
                'null'              => true,
            ],
            'title'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => true,
            ],
            'key'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
            ],
            'action'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
            ],
            'is_activated'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            
            'tag'         => [
                'type'              => 'TEXT',
            ],

            'show'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'click'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],

            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ],
            'expired_at'         => [
                'type'              => 'DATE'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('ads');
    }

    public function down()
    {
        $this->forge->dropTable('ads');
    }
}
