<?php

use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

	public function run()
	{
		Model::unguard();

		// seed tenants (excluding production environment)
		if (TenantManager::multiTenancyIsActive()) $this->call(TenantsTableSeeder::class);

		$this->call(AbilityCategoriesTableSeeder::class);
		$this->call(AbilitiesTableSeeder::class);
		$this->call(RolesTableSeeder::class);
		$this->call(RoleAbilitiesTableSeeder::class);

		// seed users & roles
		if (app()->environment() !== 'production') {
			$this->call(UsersTableSeeder::class);
			$this->call(UserRolesTableSeeder::class);
		}

		Model::reguard();
	}

}
