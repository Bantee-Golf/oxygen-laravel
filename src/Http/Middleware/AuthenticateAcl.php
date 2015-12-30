<?php

namespace EMedia\Oxygen\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthenticateAcl
{
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
	public function handle($request, Closure $next, $allowedRoles = 'admin', $permissions = null)
	{

		// Pass the roles in this format -> auth.acl:roles[owner|admin],permissions[do-something]

		if (!empty($allowedRoles)) {
			$allowedRoles = str_replace(['roles[', ']'], '', $allowedRoles);
			$allowedRoles = explode('|', $allowedRoles);
		}

		$user = $this->auth->user();
		if (!$user->is($allowedRoles)) {
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			} else {
				return redirect()->route('account')->with('error', "You don't have permissions to access that page. Please contact admin.");
			}
		}

		return $next($request);
	}
}
