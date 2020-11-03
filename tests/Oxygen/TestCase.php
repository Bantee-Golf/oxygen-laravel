<?php

namespace Tests\Oxygen;

use Cviebrock\EloquentSluggable\ServiceProvider;
use ElegantMedia\OxygenFoundation\Facades\Navigator;
use ElegantMedia\OxygenFoundation\OxygenFoundationServiceProvider;
use ElegantMedia\PHPToolkit\Exceptions\FileSystem\FileNotFoundException;
use ElegantMedia\PHPToolkit\Path;
use EMedia\AppSettings\AppSettingsServiceProvider;
use EMedia\Devices\DevicesServiceProvider;
use EMedia\MultiTenant\MultiTenantServiceProvider;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Facades\File;
use Silber\Bouncer\BouncerServiceProvider;
use Tests\Traits\MocksScoutEngines;

class TestCase extends \Orchestra\Testbench\BrowserKit\TestCase
{

	public $baseUrl = 'http://localhost';

	use \EMedia\TestKit\Testbench\BacksUpApplicationDir;
	use MocksScoutEngines;

	public function setUp(): void
	{
		parent::setUp();

		// $this->withoutMockingConsoleOutput();

		$this->backupApplicationRoot();
		$this->restoreApplicationRoot();
		$this->addLaravelFiles();

		$this->mockScoutKeywordEngine();

		//
		//
		$this->beforeApplicationDestroyed(function () {
		// 	// $this->restoreApplicationRoot();
		});
	}

	protected function getEnvironmentSetUp($app)
	{
		$app['config']->set('database.default', 'testing');

		$app->useEnvironmentPath(base_path());

		$app->bootstrapWith([LoadEnvironmentVariables::class]);

		parent::getEnvironmentSetUp($app);
	}

	protected function addLaravelFiles(): void
	{
		$laravelBase = __DIR__.'/../../laravel/laravel/';
		$destDir = base_path();

		$files = [
			'app/Models/User.php',
			'app/Http/Kernel.php',
			'app/Http/Controllers/Controller.php',
			'app/Providers/AppServiceProvider.php',
			'app/Providers/AuthServiceProvider.php',
			'app/Providers/EventServiceProvider.php',
			'app/Providers/RouteServiceProvider.php',
			'routes/web.php',
			'routes/api.php',
			'config/app.php',
			'config/auth.php',
			'.env.example' => '.env',
			'README.md',
		];

		foreach ($files as $sourceFile => $destFile) {
			// if there's no source file, use the same name for both
			if (!is_string($sourceFile)) {
				$sourceFile = $destFile;
			}

			// Check if the source exists
			$sourceFile = Path::canonical($laravelBase.$sourceFile);
			if (!file_exists($sourceFile)) {
				throw new FileNotFoundException("File {$sourceFile} not found.");
			}

			$destPath = $destDir.Path::withStartingSlash($destFile);
			File::ensureDirectoryExists(dirname($destPath));

			File::copy($sourceFile, $destPath);
		}
	}

	protected function getPackageProviders($app)
	{
		return [
			BouncerServiceProvider::class,
			OxygenFoundationServiceProvider::class,
			\EMedia\Oxygen\OxygenServiceProvider::class,
			DevicesServiceProvider::class,
			AppSettingsServiceProvider::class,
			MultiTenantServiceProvider::class,
			ServiceProvider::class,
		];
	}

	protected function getPackageAliases($app)
	{
		return [
			'Navigator' => Navigator::class,
		];
	}
}
