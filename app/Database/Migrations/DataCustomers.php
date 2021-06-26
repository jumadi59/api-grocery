<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataCustomers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'            => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'first_name'    => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => TRUE
            ],
            'last_name'     => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
                'null'              => TRUE
            ],
            'gender'        => [
                'type'              => 'ENUM',
                'constraint'        => ['none', 'male', 'female'],
                'default'           => 'none',
            ],
            'date_of_birth'  => [
                'type'              => 'DATE'
            ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->addUniqueKey(['user_id']);
        $this->forge->createTable('data_customers');
    }

    public function down()
    {
        $this->forge->dropTable('data_customers');
    }
}
