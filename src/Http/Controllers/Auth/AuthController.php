<?php

namespace EMedia\Oxygen\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{

    protected $redirectTo = '/dashboard';

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;
    use UpdatesUsers;

    public function getLogin()
    {
        if (view()->exists('auth.authenticate')) {
            return view('auth.authenticate');
        }

        return view('oxygen::auth.login');
    }

    public function getRegister()
    {
        return view('oxygen::auth.register');
    }

    protected function authenticated(Request $request, $user)
    {
        // see if this login is accepting any invitation tokens
        // if we have an incoming code, let the user join that team
        $invitationsRepo = app(config('acl.invitationRepo'));
        $tenantRepo		 = app('TenantRepository');
        $roleRepo		 = app('RoleRepository');

        if ( ! empty($invitation_code = Session::get('invitation_code')) ) {
            $invite = $invitationsRepo->getValidInvitationByCode($invitation_code, true);
            if (!$invite)
                return redirect()
                    ->intended($this->redirectPath())
                    ->with('error', 'The invitation is already used or expired.');

            // see if you can get a valid tenant
            // if (($tenant = $tenantRepo->find($invite->tenant_id)) && !empty($invite->role_id)) {
            if (!empty($invite->role_id)) {
                // the RoleID should already be attached with the tenant

                if (TenantManager::multiTenancyIsActive()) {
                    $tenant = $tenantRepo->find($invite->tenant_id);
                    TenantManager::setTenant($tenant);
                    $tenant->users()->attach($user->id);
                }

                $role = $roleRepo->find($invite->role_id);

                // attach tenant and the role
                $user->roles()->attach($role->id);

                return redirect()
                    ->intended($this->redirectPath())
                    ->with('success', 'You\'ve accepted the invitation and joined the team.');
            };
        }

        // if there are no invitations, proceed as usual
        return redirect()->intended($this->redirectPath());
    }

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' =>
            [
                'getLogout', 'getProfile', 'updateProfile'
            ]
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    protected function updateValidator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255'
        ]);
    }


}
