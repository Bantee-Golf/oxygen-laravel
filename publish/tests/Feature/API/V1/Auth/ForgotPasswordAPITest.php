<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ForgotPasswordAPITest extends TestCase
{

	use DatabaseTransactions;

	public function testForgotPasswordAPIReturnsSuccess()
	{
		$headers['Accept'] = 'application/json';
		$headers['x-api-key'] = $this->getApiKey();

		$apiKey = config('oxygen.api_key');

		$this->assertEquals($headers['x-api-key'], $apiKey);

		// form params
		$data['email'] = 'apps+user@elegantmedia.com.au';

		$response = $this->post('/api/v1/password/email', $data, $headers);

		$response->assertStatus(200);

		$this->assertDatabaseHas('password_resets', [
			'email' => $data['email'],
		]);
	}

	/**
	 * @return mixed
	 * @throws RuntimeException
	 */
	protected function getApiKey()
	{
		$key = env('API_KEY', false);

		if (!$key) {
			throw new \RuntimeException("You don't have an active API_KEY on `.env` file.");
		}

		return $key;
	}
}
