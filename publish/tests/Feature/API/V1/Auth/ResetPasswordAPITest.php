<?php


namespace Tests\Feature\API\V1\Auth;

use EMedia\Devices\Auth\DeviceAuthenticator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EMedia\TestKit\Traits\InteractsWithUsers;
use Illuminate\Support\Facades\Hash;

class ResetPasswordAPITest extends \Tests\Feature\API\V1\APIBaseTestCase
{

	use DatabaseTransactions;
	use InteractsWithUsers;

	public function testResetPasswordAPIReturnSuccessd()
	{
		$headers['Accept'] = 'application/json';
		$headers['x-access-token'] = $this->getAccessToken();
		$headers['x-api-key'] = $this->getApiKey();

		// form params
		$newPassword = '_12345678';

		$data = [
			'password' => $newPassword,
			'password_confirmation' => $newPassword,
			'current_password' => '12345678',
		];

		$response = $this->post('/api/v1/password/edit', $data, $headers);

		$response->assertStatus(200);

		$user = $this->findUserByEmail($this->getDefaultEmail());
		$this->assertTrue(Hash::check($newPassword, $user->password));
	}

	protected function getAccessToken($email = null)
	{
		$email = $email ?? $this->getDefaultEMail();

		$user = $this->findUserByEmail($email);

		$accessToken = DeviceAuthenticator::getAnAccessTokenForUserId($user->id);

		return $accessToken;
	}

	protected function getDefaultEmail()
	{
		return 'apps+user@elegantmedia.com.au';
	}
}
