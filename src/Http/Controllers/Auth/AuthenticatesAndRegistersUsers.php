<?php

namespace EMedia\Oxygen\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

trait AuthenticatesAndRegistersUsers
{
	use AuthenticatesUsers, RegistersUsers {
		AuthenticatesUsers::redirectPath insteadof RegistersUsers;
	}
}
