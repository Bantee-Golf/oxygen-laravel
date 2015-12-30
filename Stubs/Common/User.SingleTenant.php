<?php

namespace App;

use EMedia\Oxygen\Entities\Traits\OxygenUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

	use OxygenUserTrait;

	protected $fillable = ['name', 'email', 'password'];
	protected $hidden   = ['password', 'remember_token'];

}
