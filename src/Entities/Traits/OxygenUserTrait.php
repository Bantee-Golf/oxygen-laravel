<?php

namespace EMedia\Oxygen\Entities\Traits;

use Silber\Bouncer\Database\HasRolesAndAbilities;

trait OxygenUserTrait
{

	use HasRolesAndAbilities {
		HasRolesAndAbilities::isA as bouncerIsA;
	}

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

}