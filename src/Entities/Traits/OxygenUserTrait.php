<?php

namespace EMedia\Oxygen\Entities\Traits;

use Silber\Bouncer\Database\HasRolesAndAbilities;

trait OxygenUserTrait
{

	use HasRolesAndAbilities;

	public function hasFirstName()
	{
		return (empty($this->name))? false: true;
	}

}