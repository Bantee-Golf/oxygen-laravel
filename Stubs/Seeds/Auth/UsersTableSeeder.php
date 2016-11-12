<?php

use App\User;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

	public function run()
	{
		if (app()->environment() !== 'production') {
			$this->seedTestUsers();
		}
	}

	public function seedTestUsers()
	{
		$user = User::create([
			'name'	 => 'Peter Parker',
			'email'	 => 'info@elegantmedia.com.au',
			'password' => bcrypt('123456')
		]);

		if (TenantManager::multiTenancyIsActive())
		{
			$tenant = app(config('auth.tenantModel'))->find(1);
			TenantManager::setTenant($tenant);
			$user->tenants()->save($tenant);
		}

		// new user
		$user = User::create([
			'name'	 => 'Clarke Kent',
			'email'	 => 'info+kent@elegantmedia.com.au',
			'password' => bcrypt('123456')
		]);

		if (TenantManager::multiTenancyIsActive())
		{
			$tenant = app(config('auth.tenantModel'))->find(2);
			TenantManager::setTenant($tenant);
			$user->tenants()->save($tenant);

//			$adminRole = app($roleModel)->create([
//					'name'			=> 'admin',
//					'display_name'	=> 'Admin',
//					'description'	=> 'Admin project'
//			]);
		}
	}

}
