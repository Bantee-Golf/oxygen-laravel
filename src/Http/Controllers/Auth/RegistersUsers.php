<?php

namespace EMedia\Oxygen\Http\Controllers\Auth;

use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
		return view('oxygen::auth.register');
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
		$invitationsRepo = app(config('acl.invitationRepo'));
		$tenantRepo		 = app('TenantRepository');
		$roleRepo		 = app('RoleRepository');

		if ( ! empty($invitation_code = Session::get('invitation_code')) ) {
			$invite = $invitationsRepo->getValidInvitationByCode($invitation_code, true);
			if (!$invite)
				return redirect()
						->back()
						->withInput($request->except('password', 'confirm_password'))
						->with('error', 'The invitation is already used or expired. Please login or register for a new account.');
			if (TenantManager::multiTenancyIsActive()) $tenant = $tenantRepo->find($invite->tenant_id);
		} else {
			// create a tenant
			if (TenantManager::multiTenancyIsActive()) $tenant = $tenantRepo->create($request->all());
		}

		if (TenantManager::multiTenancyIsActive()) {
			TenantManager::setTenant($tenant);

			// create a user and attach to tenant
			$user = $this->create($request->all());
			$tenant->users()->attach($user->id);
		} else {
			$user = $this->create($request->all());
		}

		// assign this user as the admin of the tenant
		if ( ! empty($invite->role_id) ) {
			$user->roles()->attach($invite->role_id);
			// since the tenant is set now, we can retrieve the correct invitation as Eloquent
			$invite = $invitationsRepo->getValidInvitationByCode($invitation_code);
			$invitationsRepo->claim($invite);
			Session::flash('success', 'Your account has been created and you\'ve accepted the invitation');
		} else {
			// add the default Roles
			$defaultRoles = config('acl.defaultRoles');
			foreach ($defaultRoles as $defaultRole) {
				// create the default roles if they don't exist
				// this can be Seeded for single-tenants, but required in multi-tenancy
				$role = $roleRepo->findByName($defaultRole['name']);
				if (!$role) {
					$role = $roleRepo->newModel();
					$role->fill($defaultRole);
					$role->name = $defaultRole['name'];
					$role->save();
				}

				// add this role when the user registers
				if ($defaultRole['assignWhenRegister']) $user->roles()->attach($role->id);
			}
			Session::flash('success', 'Your account has been created and you\'re now logged in.');
		}

		Auth::login($user);

		return redirect($this->redirectPath());
	}
}
