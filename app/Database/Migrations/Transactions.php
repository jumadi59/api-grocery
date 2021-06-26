<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transactions extends Migration
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
            'address'         => [
                'type'              => 'TEXT',
                'null'           => true,
            ],
            'payment_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'code_transct'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 3,
            ],
            'total'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'status'         => [
                'type'           => 'ENUM',
                'constraint'     => [
                    'pending', 'settlement', 'expire', 'cancel', 'deny'
                ],
                'default'        => 'pending',
            ],
            'created_at'         => [
                'type'              => 'DATETIME'
            ],
            'updated_at'         => [
                'type'              => 'DATETIME'
            ],
            'payment_at'         => [
                'type'              => 'DATETIME',
                'null'              => TRUE
            ],
            'expired_at'         => [
                'type'              => 'DATETIME'
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
