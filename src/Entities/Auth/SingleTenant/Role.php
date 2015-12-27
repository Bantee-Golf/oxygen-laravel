<?php

namespace EMedia\Oxygen\Entities\Auth\SingleTenant;

use Silber\Bouncer\Database\Role as BouncerRole;

class Role extends BouncerRole
{

	protected $fillable = ['name', 'display_name'];

}