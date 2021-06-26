<?php

namespace App\Database\Seeds;

class VariantsSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $datas = [
            [
                'name' => 'Urat',
                'product_id' => 4,
                'thumb_index' => 0,
                'price' => 15000,
            ], [
                'name' => 'Jumbo',
                'product_id' => 4,
                'thumb_index' => 1,
                'price' => 25000,
            ], [
                'name' => 'Lopster',
                'product_id' => 4,
                'thumb_index' => 2,
                'price' => 20000,
            ],
        ];
        $this->db->table('variants')->insertBatch($datas);
    }
}
