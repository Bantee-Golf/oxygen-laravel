<?php

namespace Tests\Oxygen\Installer;

use Tests\Oxygen\Dashboard\InstallAndMigrate;

class OxygenInstallerTest extends \Tests\Oxygen\TestCase
{

	use InstallAndMigrate;

	public function testInstallerRuns(): void
	{
		$this->installAndMigrate();

		$this->assertFileExists(app_path('Providers/OxygenServiceProvider.php'));
	}
}
