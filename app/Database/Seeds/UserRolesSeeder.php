<?php

namespace App\Database\Seeds;

class UserRolesSeeder extends \CodeIgniter\Database\Seeder
{
	public function run()
	{
		$datas = [
			[
				'user_id'           => 1,
				'role_id'           => 1
			],
			[
				'user_id'            => 2,
				'role_id'           => 2
			],
			[
				'user_id'            => 3,
				'role_id'            => 2
			],
			[
				'user_id'            => 4,
				'role_id'            => 2
			]
		];
		$this->db->table('user_roles')->insertBatch($datas);
	}
}
