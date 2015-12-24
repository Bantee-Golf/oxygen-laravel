<?php


namespace EMedia\Oxygen\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Guard;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordController extends Controller
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

	/**
	 * Create a new password controller instance.
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

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return \Illuminate\Http\Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token)) {
			throw new NotFoundHttpException;
		}

		return view('oxygen::auth.reset')->with('token', $token);
	}

	/**
	 * Display the form to request a password reset link.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function getEmail()
	{
		return view('oxygen::auth.password');
	}
}
