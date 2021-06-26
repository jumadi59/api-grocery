<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CouponClaims extends Migration
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
            'coupon_id'         => [
                'type'              => 'INT',
                'constraint'        => 12,
            ],
            'is_used'         => [
                'type'              => 'TINYINT',
                'constraint'        => 1,
            ],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('coupon_claims');
    }

    public function down()
    {
        $this->forge->dropTable('coupon_claims');
    }
}
