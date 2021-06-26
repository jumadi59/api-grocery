<?php

namespace App\Database\Seeds;

class UsersSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $datas = [
            [
                'username'          => 'Admin',
                'password'          => password_hash('12345678', PASSWORD_BCRYPT),
                'email'             => 'admin@gocery.test',
                'phone'             => '087654322113',
                'avatar'            => 'profile_user-default.png',
                'verified_email'    => true,
                'verified_phone'    => true,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'username'          => 'Indra66',
                'password'          => password_hash('12345678', PASSWORD_BCRYPT),
                'email'             => 'indra66@gmail.com',
                'phone'             => '0831318888',
                'avatar'            => 'profile_user-default.png',
                'verified_email'    => true,
                'verified_phone'    => true,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'username'          => 'Sugandi77',
                'password'          => password_hash('12345678', PASSWORD_BCRYPT),
                'email'             => 'sugandi77@gmail.com',
                'phone'             => '0852222211',
                'avatar'            => 'profile_user-default.png',
                'verified_email'    => true,
                'verified_phone'    => true,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ],
            [
                'username'          => 'Widodo',
                'password'          => password_hash('12345678', PASSWORD_BCRYPT),
                'email'             => 'widodo@gmail.com',
                'phone'             => '0852222211',
                'avatar'            => 'profile_user-default.png',
                'verified_email'    => true,
                'verified_phone'    => true,
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('users')->insertBatch($datas);
    }
}
