<?php

namespace App\Database\Seeds;

class AddressSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $datas = [
            [
                'user_id' => 1,
                'primary' => true,
                'label' => 'store',
                'postal_code' => 38386,
                'street' => 'Jl. Bengkulu - Curup km36',
                'province' => 'Bengkulu',
                'city' => 'Kaur',
                'subdistrict' => 'Muara Sahung',
                'latitude' => -3.696221,
                'longitude' => 102.525393
            ],
            [
                'user_id' => 2,
                'label' => 'store',
                'primary' => true,
                'postal_code' => 38385,
                'street' => 'Jl. Bengkulu - Curup km29',
                'province' => 'Bengkulu',
                'city' => 'Bengkulu Tengah',
                'subdistrict' => 'Karang Tinggi',
                'latitude' => -3.696221,
                'longitude' => 102.525393
            ],
            [
                'user_id' => 2,
                'label' => 'home',
                'primary' => true,
                'postal_code' => 38384,
                'street' => 'Jl. Bengkulu - Curup km30',
                'province' => 'Bengkulu',
                'city' => 'Bengkulu Tengah',
                'subdistrict' => 'Pondok Kelapa',
                'latitude' => -3.696221,
                'longitude' => 102.525393
            ]
        ];
        $this->db->table('address')->insertBatch($datas);
    }
}
