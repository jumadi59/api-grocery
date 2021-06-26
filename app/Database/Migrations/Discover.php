<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Discover extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => [
                'type'              => 'VARCHAR',
                'constraint'        => 12,
            ],
            'title'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 20,
            ],
            'sub_title'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 50,
            ],
            'banner'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
                'null'              => true,
            ],
            'banner_left'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 128,
                'null'              => true,
            ],
            'background_color'      => [
                'type'              => 'VARCHAR',
                'constraint'        => 20,
                'null'              => true,
            ],
            'filters'      => [
                'type'              => 'TEXT',
            ],
            
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('discover');
    }

    public function down()
    {
        $this->forge->dropTable('discover');
    }
}
