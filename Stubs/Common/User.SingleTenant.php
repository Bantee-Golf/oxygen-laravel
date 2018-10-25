<?php

namespace App;

use EMedia\Oxygen\Entities\Traits\OxygenUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

	use OxygenUserTrait;
	use Notifiable;

	protected $searchable = ['name', 'last_name', 'email'];

	protected $fillable = ['name', 'last_name', 'email', 'password'];
	protected $visible  = ['uuid', 'name', 'last_name', 'email', 'full_name'];
	protected $hidden   = ['password', 'remember_token'];
	protected $appends  = ['full_name'];


}
