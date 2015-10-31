<?php

namespace App\Entities\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Tenant extends Model
{

    protected $fillable = ['company_name'];

	public function users()
	{
		return $this->belongsToMany(Config::get('auth.model'));
    }
}
