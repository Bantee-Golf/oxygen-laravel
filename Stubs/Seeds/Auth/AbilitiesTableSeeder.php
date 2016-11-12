<?php

use App\Entities\Auth\AbilityCategory;
use Cocur\Slugify\Slugify;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class AbilitiesTableSeeder extends Seeder
{

	public function run()
	{
		$this->seedAbilitiesFromCategories();
	}

	public function seedAbilitiesFromCategories()
	{
		$categories = AbilityCategory::all();

		foreach ($categories as $category) {
			$abilities = json_decode($category->default_abilities);

			foreach ($abilities as $abilityName) {
				// $slug = (new Slugify())->slugify($abilityName);

				$ability = app(config('auth.abilityModel'));
				$ability->title = $abilityName;
				$ability->ability_category_id = $category->id;

				$existingAbility = app(config('auth.abilityModel'))
					->where('title', $abilityName)
					->where('ability_category_id', $category->id)
					->first();

				if ($existingAbility) {
					// leave it alone
				} else {
					$ability->save();
				}
			}
		}
	}



}
