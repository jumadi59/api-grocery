<?php namespace App\Database\Migrations;
 
use CodeIgniter\Database\Migration;
 
class Couriers extends Migration
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
                'constraint'        => 100,
            ],
            'simple_name'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 20,
            ],
            'icon'         => [
                'type'              => 'VARCHAR',
                'constraint'        => 100,
            ],
            'is_activated'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
            'is_cod'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('couriers');
    }
 
    public function down()
    {
        $this->forge->dropTable('couriers');
    }
}