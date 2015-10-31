<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Authenticate {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (Config::get('app.enableAuthentication'))
		{
			if ($this->auth->guest())
			{
				if ($request->ajax() || $request->wantsJson())
				{
					$response = [
						'result'	=> false,
						'message'	=> 'You need to login to access this data. If you logged-in already, your session may have been expired. Please try to login again.',
						'type'		=> 'UNAUTHORIZED_USER'
					];
					return response($response, 401);
				}
				else
				{
					return redirect()->guest('auth/login');
				}
			}
		}
		else
		{
			// TODO: handle multiple tenants
			$user = $this->auth->user();
			App::make('TenantManager')->setTenant($user->tenants()->first());
		}


		return $next($request);
	}

}
