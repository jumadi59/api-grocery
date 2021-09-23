<?php

namespace App\Database\Seeds;

class AdsSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        helper('text');
        $date = date_create(date('Y-m-d'));
        date_add($date, date_interval_create_from_date_string('7 days'));
        $expired = date_format($date, 'Y-m-d');
        $datas = [
            [
                'title' => 'Free Shipping',
                'image' => 'banner_slide.jpg',
                'is_activated' => 1,
                'ads' => 'coupon',
                'type' => 'slide',
                'action' => '1',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
            [
                'title' => 'Discount all products',
                'image' => 'banner_slide_1.jpg',
                'is_activated' => 1,
                'ads' => 'coupon',
                'type' => 'slide',
                'action' => '2',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
            [
                'title' => 'Coupon 3',
                'image' => 'banner_slide_3.jpg',
                'is_activated' => 1,
                'ads' => 'coupon',
                'type' => 'slide',
                'action' => '3',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
            [
                'title' => 'Coupon 4',
                'image' => 'banner_slide_4.jpg',
                'is_activated' => 1,
                'ads' => 'coupon',
                'type' => 'slide',
                'action' => '4',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
            [
                'title' => '',
                'image' => 'banner_dialog.png',
                'is_activated' => 1,
                'ads' => 'store',
                'type' => 'dialog',
                'action' => '1',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
            [
                'title' => null,
                'image' => null,
                'is_activated' => 1,
                'ads' => 'product',
                'type' => 'top',
                'action' => '1',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
            [
                'title' => null,
                'image' => null,
                'is_activated' => 1,
                'ads' => 'product',
                'type' => 'top',
                'action' => '5',
                'key' => random_string(),
                'expired_at' => $expired,
            ],
        ];
        $this->db->table('ads')->insertBatch($datas);
    }
}
