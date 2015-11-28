<?php

namespace App\Entities\Auth;

use EMedia\MultiTenant\Scoping\Traits\TenantScopedModelTrait;
use Illuminate\Support\Facades\Config;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{

	protected $fillable = ['name', 'description'];

	use TenantScopedModelTrait;

	public function users()
	{
		return $this->belongsToMany(Config::get('auth.model'));
	}
}