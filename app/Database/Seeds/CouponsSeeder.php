<?php

namespace App\Database\Seeds;

use CodeIgniter\I18n\Time;

class CouponsSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $time = new Time();
        $now = $time->toDateTimeString();
        $time->modify('+7 day');
        $datas = [
            [
                'store_id'          => null,
                'banner'            => 'banner_slide.jpg',
                'name'              => 'Free Shipping',
                'code'              => 'GRAONGKIR',
                'min_transaction'   => 0,
                'value'             => 0,
                'unit'              => 'amount',
                'type'              => 'free_shipping',
                'target'            => null,
                'target_id'         => null,
                'platfrom'          => 'mobile',
                'stock'             => null,
                'valid_at'          => $now,
                'expired_at'        => null
            ], [
                'store_id'          => 1,
                'banner'            => 'banner_slide_1.jpg',
                'name'              => 'Discount all products',
                'code'              => 'DISCOUNTPRODUCT',
                'min_transaction'   => 20000,
                'value'             => 20,
                'unit'              => 'percentage',
                'type'              => 'discount',
                'target'            => 'store',
                'target_id'         => 2,
                'platfrom'          => 'mobile',
                'stock'             => 200,
                'valid_at'          => $now,
                'expired_at'        => $time->toDateTimeString()
            ], [
                'store_id'          => 1,
                'banner'            => 'banner_slide_3.jpg',
                'name'              => 'Discount 4',
                'code'              => 'DISCOUNTPRODUCT',
                'min_transaction'   => 20000,
                'value'             => 20,
                'unit'              => 'percentage',
                'type'              => 'discount',
                'target'            => 'category',
                'target_id'         => 2,
                'platfrom'          => 'mobile',
                'stock'             => 200,
                'valid_at'          => $now,
                'expired_at'        => $time->toDateTimeString()
            ], [
                'store_id'          => 1,
                'banner'            => 'banner_slide_4.jpg',
                'name'              => 'Discount fruit',
                'code'              => 'DISCOUNTPRODUCT',
                'min_transaction'   => 20000,
                'value'             => 20,
                'unit'              => 'percentage',
                'type'              => 'discount',
                'target'            => 'category',
                'target_id'         => 2,
                'platfrom'          => 'mobile',
                'stock'             => 200,
                'valid_at'          => $now,
                'expired_at'        => $time->toDateTimeString()
            ]
        ];
        $this->db->table('coupons')->insertBatch($datas);
    }
}
