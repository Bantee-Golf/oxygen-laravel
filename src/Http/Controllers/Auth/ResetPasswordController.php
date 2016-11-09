<?php

namespace EMedia\Oxygen\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;
	protected $redirectTo = '/dashboard';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->auth = $auth;
		$this->passwords = $passwords;

		$this->middleware('guest', ['except' =>
			[
				'getUpdate', 'postUpdate'
			]
		]);
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * If no token is present, display the link request form.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string|null  $token
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function showResetForm(Request $request, $token = null)
	{
		return view('oxygen::auth.passwords.reset')->with(
			['token' => $token, 'email' => $request->email]
		);
	}


	public function getUpdate()
	{
		$user = Auth::user();
		return view('oxygen::account.password-edit', compact('user'));
	}

	/**
	 * Update the current logged-in user's password
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function postUpdate(Request $request)
	{
		$user = $this->auth->user();


		// validate current password
		$isPasswordValid = $this->auth->attempt([
			'email'		=> $user->email,
			'password'	=> $request->get('current_password')
		]);

		if ( ! $isPasswordValid)
			return redirect()->back()->withErrors(['Current password is incorrect.']);

		$this->validate($request, [
			'password'	=> 'required|confirmed|min:6'
		]);

		// set the new password
		$user->password = bcrypt($request->get('password'));
		if ( ! $user->save())
			return redirect()->back()->withErrors(['Failed to save the new password. Try with another password.']);

		return redirect()->back()->with('success', 'Password successfully updated.');
	}
}
