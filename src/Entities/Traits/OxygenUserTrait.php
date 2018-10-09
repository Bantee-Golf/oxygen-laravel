<?php

namespace EMedia\Oxygen\Entities\Traits;

use App\Entities\Auth\Ability;
use App\Entities\Auth\Role;

use Silber\Bouncer\Database\HasRolesAndAbilities;
use Silber\Bouncer\Database\Models;

trait OxygenUserTrait
{

	use HasRolesAndAbilities {
		HasRolesAndAbilities::isA as bouncerIsA;
	}

	public function getProfileUpdatableFields()
	{
		return [
			'name',
		];
	}

	public function getFullNameAttribute()
	{
		return implode_not_empty(' ', [$this->name, $this->last_name]);
	}

	// use Authorizable;

	/**
	 * Override the Bouncer trait's is function and allow passing in an array of roles
	 * So you can send $user->isA(['admin', 'owner']) as well as $user->isA('admin', 'owner')
	 *
	 * @param $roles
	 * @return bool|mixed
	 */
	public function isA($roles)
	{

		if (is_array($roles)) {
			return call_user_func_array([$this, 'bouncerIsA'], $roles);
		}

		return $this->bouncerIsA($roles);
	}

	public function hasFirstName()
	{
		return (empty($this->name))? false: true;
	}

	/**
	 *  Setup model event hooks
	 */
	public static function boot()
	{
		parent::boot();
		self::creating(function ($model) {
			$model->uuid = (string) \Webpatser\Uuid\Uuid::generate(4);
		});
	}

}