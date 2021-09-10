<?php

namespace App\Entities\Auth;

use Cviebrock\EloquentSluggable\Sluggable;
use ElegantMedia\OxygenFoundation\Scout\KeywordSearchable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class AbilityCategory extends Model implements KeywordSearchable
{

	use Searchable;
	use Sluggable;

	protected $fillable = [
		'name',
		'default_abilities'
	];

	public function sluggable(): array
	{
		return [
			'slug' => [
				'source' => 'name'
			]
		];
	}

	public function getSearchableFields(): array
	{
		return [
			'name',
		];
	}

	public function abilities()
	{
		return $this->hasMany(app(config('oxygen.abilityModel')));
	}
}
