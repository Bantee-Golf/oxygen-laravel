<?php

namespace App\Http\Controllers\Auth;

use App\Entities\Auth\Role;
use App\Entities\Auth\Tenant;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

trait RegistersUsers
{
	use RedirectsUsers;

	/**
	 * Show the application registration form.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getRegister()
	{
		return view('auth.register');
	}

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postRegister(Request $request)
	{
		$validator = $this->validator($request->all());
		$invitation_code = null;

		if ($validator->fails()) {
			$this->throwValidationException(
				$request, $validator
			);
		}

		// if we have an incoming code, let the user join that team
		$invitationsRepo = App::make('AppAdmin\Entities\Invitations\InvitationRepository');
		if ( ! empty($invitation_code = Session::get('invitation_code')) ) {
			$invite = $invitationsRepo->getValidInvitationByCode($invitation_code, true);
			if (!$invite)
				return redirect()
							->back()
							->withInput($request->except('password', 'confirm_password'))
							->with('error', 'The invitation is already used or expired. Please login or register for a new account.');
			$tenant = Tenant::find($invite->tenant_id);
		} else {
			// create a tenant
			$tenant = Tenant::create($request->all());
		}

		TenantManager::setTenant($tenant);

		// create a user and attach to tenant
		$user = $this->create($request->all());
		$tenant->users()->attach($user->id);

		// assign this user as the admin of the tenant
		if ( ! empty($invite->role_id) ) {
			$user->roles()->attach($invite->role_id);
			// since the tenant is set now, we can retrieve the correct invitation as Eloquent
			$invite = $invitationsRepo->getValidInvitationByCode($invitation_code);
			$invitationsRepo->claim($invite);
			Session::flash('success', 'Your account has been created and you\'ve accepted the invitation');
		} else {
			// add the default Roles
			$defaultRoles = Config::get('multiTenant.defaultRoles');
			foreach ($defaultRoles as $defaultRole) {
				$role = new Role();
				$role->fill($defaultRole);
				$role->name = $defaultRole['name'];
				$role->save();

				// add this user as the default Owner
				if ($defaultRole['name'] == 'owner') $user->roles()->attach($role->id);
			}
		}

		Auth::login($user);

		return redirect($this->redirectPath());
	}
}
