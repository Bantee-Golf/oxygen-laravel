<?php

namespace App\Entities\Auth;

use Cviebrock\EloquentSluggable\Sluggable;
use EMedia\Oxygen\Entities\Auth\SingleTenant\Ability as AbilityBase;

class Ability extends AbilityBase
{

	use Sluggable;

	protected $fillable = ['name', 'title'];

	public function sluggable(): array
	{
		return [
			'name' => [
				'source' => 'title',
				'maxLength' => 150,
			]
		];
	}

	public function category()
	{
		return $this->belongsTo(AbilityCategory::class, 'ability_category_id');
	}

}
