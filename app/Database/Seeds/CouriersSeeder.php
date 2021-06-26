<?php

namespace App\Database\Seeds;

class CouriersSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $datas = [[
            'name' => 'POS Indonesia (POS)',
            'simple_name' => 'POS',
            'code' => 'pos',
            'icon' => 'pos.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'Ninja Xpress (NINJA)',
            'simple_name' => 'Ninja',
            'code' => 'ninja',
            'icon' => 'ninja.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'SiCepat Express (SICEPAT)',
            'simple_name' => 'SiCepat',
            'code' => 'sicepat',
            'icon' => 'sicepat.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'J&T Express (J&T)',
            'simple_name' => 'J&T',
            'code' => '',
            'icon' => 'jandt.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'Lion Parcel (LION)',
            'simple_name' => 'Lion',
            'code' => 'lion',
            'icon' => 'lion.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'Wahana Prestasi Logistik (WAHANA)',
            'simple_name' => 'Wahana',
            'code' => 'wahana',
            'icon' => 'wahana.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'Citra Van Titipan Kilat (TIKI)',
            'simple_name' => 'TIKI',
            'code' => 'tiki',
            'icon' => 'wahana.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'Jalur Nugraha Ekakurir (JNE)',
            'simple_name' => 'JNE',
            'code' => 'jne',
            'icon' => 'wahana.png',
            'is_activated' => true,
            'is_cod' => false,
        ], [
            'name' => 'Foodiez Express',
            'simple_name' => 'Foodiez Express',
            'code' => 'fe',
            'icon' => 'foodiez.png',
            'is_activated' => true,
            'is_cod' => true,
        ]];
        $this->db->table('couriers')->insertBatch($datas);
    }
}
