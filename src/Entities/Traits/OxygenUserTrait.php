<?php

namespace EMedia\Oxygen\Entities\Traits;

use Silber\Bouncer\Database\HasRolesAndAbilities;

trait OxygenUserTrait
{

	use HasRolesAndAbilities;

	/**
	 * Override the Bouncer trait's is function and allow passing in an array of roles
	 * So you can send $user->is(['admin', 'owner']) as well as $user->is('admin', 'owner')
	 *
	 * @param $roles
	 * @return bool|mixed
	 */
	public function is($roles)
	{

		if (is_array($roles)) {
			return call_user_func_array([$this, 'isAll'], $roles);
		}

		return $this->isA($roles);
	}

	public function hasFirstName()
	{
		return (empty($this->name))? false: true;
	}

}