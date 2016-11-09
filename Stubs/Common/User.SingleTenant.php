<?php

namespace App;

use EMedia\Oxygen\Entities\Traits\OxygenUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

	use OxygenUserTrait;
	use Notifiable;

	protected $fillable = ['name', 'email', 'password'];
	protected $hidden   = ['password', 'remember_token'];

}
