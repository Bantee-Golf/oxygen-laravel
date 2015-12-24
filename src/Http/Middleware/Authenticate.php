<?php

namespace EMedia\Oxygen\Http\Middleware;

use Closure;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

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
		if (config('app.enableAuthentication'))
		{
			if ($this->auth->guest())
			{
				return $this->rejectRequest($request);
			}
		}
		else
		{
			$user = $this->auth->user();

			// login to a default account for testing
			if (!$user && App::environment() == 'local') {
				$user = $this->auth->loginUsingId(1);
			}

			if (!$user) return $this->rejectRequest($request);

			// DONE: handle multiple tenants and save in session
			// TODO: MUST check acceptInvite() in InvitationsController
			if (TenantManager::isTenantNotSet()) TenantManager::setTenant($user->tenants()->first());

		}

		if ($user = $this->auth->user()) View::share('user', $user);
		if (TenantManager::isTenantSet()) {
			$tenant = TenantManager::getTenant();
			View::share('tenant', $tenant);
		}

		return $next($request);
	}

	protected function rejectRequest($request)
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
