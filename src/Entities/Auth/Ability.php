<?php


namespace EMedia\Oxygen\Entities\Auth;

use EMedia\MultiTenant\Scoping\Traits\TenantScopedModelTrait;
use Silber\Bouncer\Database\Ability as BouncerAbility;

class Ability extends BouncerAbility
{

	use TenantScopedModelTrait;

}