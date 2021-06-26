<?php

namespace App\Database\Seeds;

class RolesSeeder extends \CodeIgniter\Database\Seeder
{
	public function run()
	{
		$datas = [
			[
				'name'        => 'admin',
				'description' => 'Administrator'
			],
			[
				'name'        => 'members',
				'description' => 'General User'
			]
		];
		$this->db->table('roles')->insertBatch($datas);
	}
}
