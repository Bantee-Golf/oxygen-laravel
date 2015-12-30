<?php

use App\User;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

	public function run()
	{
		$roleModel = config('auth.roleModel');
		if (!$roleModel) throw new BindingResolutionException('auth.roleModel is not configured in settings');

		$user = User::create([
				'name'	 => 'Peter Parker',
				'email'	 => 'shane.emedia@gmail.com',
				'password' => bcrypt('123456')
		]);

		if (TenantManager::multiTenancyIsActive())
		{
			$tenant = app(config('auth.tenantModel'))->find(1);
			TenantManager::setTenant($tenant);
			$user->tenants()->save($tenant);
		}

		$adminRole = app($roleModel)->create([
				'name'			=> 'admin',
				'display_name'	=> 'Admin',
				'description'	=> 'Admin project'
		]);
		$user->roles()->save($adminRole);

		$ownerRole = app($roleModel)->create([
				'name'			=> 'owner',
				'display_name'	=> 'Owner',
				'description'	=> 'Owners of the account'
		]);
		$user->roles()->save($ownerRole);

		$role = app($roleModel)->create([
				'name'			=> 'member',
				'display_name'	=> 'Member',
				'description'	=> 'Members project'
		]);

		$role = app($roleModel)->create([
				'name'			=> 'tech-support',
				'display_name'	=> 'Tech Support',
				'description'	=> 'Tech support team'
		]);


		// new user
		$user = User::create([
				'name'	 => 'Clarke Kent',
				'email'	 => 'shane.emedia+kent@gmail.com',
				'password' => bcrypt('123456')
		]);

		if (TenantManager::multiTenancyIsActive())
		{
			$tenant = app(config('auth.tenantModel'))->find(2);
			TenantManager::setTenant($tenant);
			$user->tenants()->save($tenant);

			$adminRole = app($roleModel)->create([
					'name'			=> 'admin',
					'display_name'	=> 'Admin',
					'description'	=> 'Admin project'
			]);
		}

		$user->roles()->save($adminRole);

	}

}
