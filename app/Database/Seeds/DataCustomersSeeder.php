<?php

namespace App\Database\Seeds;

class DataCustomersSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $datas = [
            [
                'user_id'       => 1,
                'first_name'    => 'Admin',
                'last_name'     => 'istrator',
                'gender'        => 'male',
                'date_of_birth' => '1998-03-20',
            ],
            [
                'user_id'       => 2,
                'first_name'    => 'Indra',
                'last_name'     => 'Hartono',
                'gender'        => 'male',
                'date_of_birth' => '1997-03-20',
            ],
            [
                'user_id'       => 3,
                'first_name'    => 'Sugandi',
                'last_name'     => 'Heriantoni',
                'gender'        => 'male',
                'date_of_birth' => '1997-06-12',
            ],
            [
                'user_id'       => 4,
                'first_name'    => 'Widodo',
                'last_name'     => 'Corneo',
                'gender'        => 'male',
                'date_of_birth' => '1998-12-12',
            ]
        ];
        $this->db->table('data_customers')->insertBatch($datas);
    }
}
