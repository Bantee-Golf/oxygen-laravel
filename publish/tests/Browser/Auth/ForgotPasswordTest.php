<?php

namespace Tests\Browser\Auth;

use App\Providers\RouteServiceProvider;
use EMedia\TestKit\Traits\InteractsWithUsers;
use Laravel\Fortify\Features;
use Tests\Browser\Pages\ForgotPassword;
use Tests\Browser\Pages\Login;
use Tests\DuskTestCase;
use Tests\Browser\Pages\ResetPassword;
use Laravel\Dusk\Browser;

class ForgotPasswordTest extends DuskTestCase
{

	use InteractsWithUsers;

	/**
	 * It should not allow sending password reset email for invalid email.
	 *
	 * @return void
	 */
	public function testNotSendingForInvalidEmails(): void
	{
		$this->browse(function (Browser $browser) {
			$browser->visit(new ForgotPassword())
				->type('@email', 'johndoe.com')
				->click('@submit')
				->assertPathIs('/password/forgot');
		});
	}

	/**
	 * It should not allow sending password reset email for non-existing email.
	 *
	 * @return void
	 */
	public function testNotSendingForNonExistingEmails(): void
	{
		$this->browse(function (Browser $browser) {
			$browser->visit(new ForgotPassword())
				->type('@email', 'xyz@nonexisting.com')
				->click('@submit')
				->assertPathIs('/password/forgot')
				->assertSee(trans('passwords.user'));
		});
	}

	/**
	 * It should send password reset email to existing email.
	 *
	 * @return void
	 */
	public function testSendingForExistingEmails(): void
	{
		$this->browse(function (Browser $browser) {
			$browser->visit(new ForgotPassword())
				->type('@email', 'apps+user@elegantmedia.com.au')
				->click('@submit')
				->waitForRoute('password.request', [], 15)
				->assertSee(trans('passwords.sent'));
		});
	}

	public function testForgotPasswordGeneratesValidResetLink(): void
	{
		$this->withoutMiddleware('throttle');

		$user = $this->findUserByEmail('apps@elegantmedia.com.au');

		$passwordBroker = $this->app->make('auth.password.broker');
		$token = $passwordBroker->createToken($user);

		$this->assertTrue($passwordBroker->tokenExists($user, $token));

		$this->browse(function (Browser $browser) use ($user, $token) {
			$newPassword = '123-123-123';

			$browser->visit(route('password.reset', $token))
				->assertPathBeginsWith('/password/reset')
				->assertSee('Reset Password')
				->type('#email', $user->email)
				->type('#password', $newPassword)
				->type('#password-confirm', $newPassword)
				->click('#reset-button')
				->assertDontSee('token is invalid')
				->waitForRoute('login', [], 5)
				->assertRouteIs('login')
				->assertSee(trans('passwords.reset'));

			// check the user can't login with old password after a change
			$browser->visit(new Login())
				->type('@email', $user->email)
				->type('@password', '12345678')
				->click('@submit')
				->assertPathIs('/login')
				->assertSee('These credentials do not match our records')
				->pause(1000);

			// check user can login with new password
			$browser->visit(new Login())
				->type('@email', $user->email)
				->type('@password', $newPassword)
				->click('@submit')
				->assertDontSeeLink('Login');

			// if email verification is enabled, the user should get redirected to `verification.notice` link
			if (Features::enabled(Features::emailVerification())) {
				$browser->assertRouteIs('verification.notice');
			} else {
				$browser->assertPathIs(RouteServiceProvider::HOME);
			}
			$browser->click('#navbarDropdown')
				->click('#logout');
			$browser->assertSee(config('app.name'));

			// restore password
			$user->password = bcrypt('12345678');
			$user->save();
		});
	}

	/**
	 *
	 * Reset the changes
	 *
	 */
	protected function tearDown() : void
	{
		\DB::table('password_resets')->where('email', 'apps+user@elegantmedia.com.au')->delete();

		parent::tearDown();
	}
}
