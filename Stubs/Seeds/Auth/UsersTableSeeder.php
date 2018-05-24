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
		$users = [
			[
				'name'	 => 'Peter Parker',
				'email'	 => 'info@elegantmedia.com.au',
				'password' => bcrypt('123456')
			],
			[
				'name'	 => 'Clarke Kent',
				'email'	 => 'shane.emedia+kent@gmail.com',
				'password' => bcrypt('123456')
			],
		];

		foreach ($users as $key => $data) {
			if (!$user = User::where('email', $data['email'])->first()) {
				$user = User::create($data);

				if (TenantManager::multiTenancyIsActive()) {
					$tenant = app(config('auth.tenantModel'))->find($i + 1);
					TenantManager::setTenant($tenant);
					$user->tenants()->save($tenant);
				}
			}
		}
	}
}
