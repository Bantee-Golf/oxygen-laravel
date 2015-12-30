<?php

namespace EMedia\Oxygen\Entities\Auth\MultiTenant;

use EMedia\MultiTenant\Scoping\Traits\TenantScopedModelTrait;
use Silber\Bouncer\Database\Role as BouncerRole;

class Role extends BouncerRole
{

	protected $fillable = ['name', 'display_name', 'description'];

	use TenantScopedModelTrait;

}