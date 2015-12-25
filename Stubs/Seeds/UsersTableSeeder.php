<?php

use App\User;
use EMedia\MultiTenant\Facades\TenantManager;
use EMedia\Oxygen\Entities\Auth\Role;
use EMedia\Oxygen\Entities\Auth\Tenant;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

	public function run()
	{
		$user = User::create([
			'name'	 => 'Peter Parker',
			'email'	 => 'shane.emedia@gmail.com',
			'password' => bcrypt('123456')
		]);
		$tenant = Tenant::find(1);
		TenantManager::setTenant($tenant);

		$role = Role::create([
			'name'			=> 'admin',
			'display_name'	=> 'Admin',
			'description'	=> 'Admin project'
		]);
		$user->roles()->save($role);
		$user->tenants()->save($tenant);

		$role = Role::create([
			'name'			=> 'owner',
			'display_name'	=> 'Owner',
			'description'	=> 'Owners of the account'
		]);

		$role = Role::create([
			'name'			=> 'member',
			'display_name'	=> 'Member',
			'description'	=> 'Members project'
		]);

		$role = Role::create([
			'name'			=> 'tech-support',
			'display_name'	=> 'Tech Support',
			'description'	=> 'Tech support team'
		]);


		// new user
		$user = User::create([
			'name'	 => 'Clarke Kent',
			'email'	 => 'shane.emedia+john@gmail.com',
			'password' => bcrypt('123456')
		]);
		$tenant = Tenant::find(2);
		TenantManager::setTenant($tenant);

		$role = Role::create([
			'name'			=> 'admin',
			'display_name'	=> 'Admin',
			'description'	=> 'Admin project'
		]);
		$user->roles()->save($role);
		$user->tenants()->save($tenant);
	}

}
