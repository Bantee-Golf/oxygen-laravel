<?php

namespace App;

use EMedia\MultiTenant\Auth\MultiTenantUserTrait;
use EMedia\Oxygen\Entities\Traits\OxygenUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

	use MultiTenantUserTrait, OxygenUserTrait {
		OxygenUserTrait::roles insteadof MultiTenantUserTrait;
	}

	protected $fillable = ['name', 'email', 'password'];
	protected $hidden   = ['password', 'remember_token'];

}
