<?php

namespace Tests\Oxygen\Dashboard;

use App\Models\User;

class DashboardTest extends \Tests\Oxygen\TestCase
{

	use InstallAndMigrate;

	public function testHomePageLoads(): void
	{
		$this->artisan('key:generate');

		$this->runAdminInstallerCommand();

		$this->getEnvironmentSetUp($this->app);

		$this->refreshApplication();

		$this->visit('/')
			 ->see('Workbench');
	}

	public function testAdminCanLogin(): void
	{
		$this->installAndMigrate();

		$user = User::where('email', 'apps@elegantmedia.com.au')->first();
		validate_all_present($user);

		$this->assertNotNull($user);
	}
}
