<?php

namespace App\Database\Seeds;

class StoresSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $stores = array(
            array('id' => '1', 'user_id' => '1', 'name' => 'Fas Store', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'icon' => 'test_toko_1.jpg', 'thumb' => 'test_toko_1.jpg', 'courier_active' => '1,7,8,9', 'is_support_cod' => '0', 'address_id' => '1', 'rating' => '4', 'is_activated' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')),
            array('id' => '2', 'user_id' => '2', 'name' => 'Foodiez', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'icon' => 'test_toko_2.jpg', 'thumb' => 'test_toko_2.jpg', 'courier_active' => '1,7,8,9', 'is_support_cod' => '0', 'address_id' => '2', 'rating' => '3', 'is_activated' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'))
        );

        $this->db->table('stores')->insertBatch($stores);
    }
}
