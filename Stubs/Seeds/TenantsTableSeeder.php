<?php

use EMedia\Oxygen\Entities\Auth\Tenant;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TenantsTableSeeder extends Seeder
{

	public function run()
	{
		$faker = Faker::create('en_AU');

		foreach(range(1, 10) as $index)
		{
			Tenant::create([
				'company_name'	=> $faker->company
			]);
		}
	}

}
