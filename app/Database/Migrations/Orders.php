<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Orders extends Migration
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
            'transaction_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'coupon'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'courier'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'invoice'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'resi'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 15,
            ],
            'status'         => [
                'type'           => 'ENUM',
                'constraint'     => [
                    'confirmation', 'packed', 'sent', 'done', 'canceled', 'expire', 'refuse', 'taking'
                ],
                'null'           => true,
                'default'        => NULL,
            ],
            'sent_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ],
            'cenceled_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ],
            'accepted_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ],
            'expired_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}
