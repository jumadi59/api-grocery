<?php

namespace App\Database\Seeds;

class DiscountsSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $date = date_create(date('Y-m-d H:i:s'));
        date_add($date, date_interval_create_from_date_string('10 hours'));
        $expired = date_format($date, 'Y-m-d H:i:s');

        $datas = [
            [
                'store_id'      => 1,
                'value'         => 5,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 2,
                'stock'         => 20, 
                'sold'          => 0, 
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 20,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 5,
                'stock'         => 20, 
                'sold'          => 0, 
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 15,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 7,
                'stock'         => 20, 
                'sold'          => 0, 
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 5,
                'type'          => 'promo',
                'target'        => 'variant',
                'target_id'     => 10,
                'stock'         => 20, 
                'sold'          => 0, 
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 15,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 12,
                'stock'         => 20, 
                'sold'          => 0, 
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 15,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 15,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 17,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 5,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 20,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 15,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 22,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 26,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 28,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 17,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 19,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 21,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 7,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ], [
                'store_id'      => 1,
                'value'         => 30,
                'type'          => 'flash_sale',
                'target'        => 'product',
                'target_id'     => 4,
                'stock'         => 20, 
                'sold'          => 0,
                'valid_at'      => date('Y-m-d H:i:s'),
                'expired_at'    => $expired,
            ],
        ];
        $this->db->table('discounts')->insertBatch($datas);
    }
}
