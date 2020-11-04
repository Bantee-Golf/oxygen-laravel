<?php


namespace Tests\Oxygen\Dashboard;

use ElegantMedia\OxygenFoundation\Scout\KeywordSearchEngine;
use ElegantMedia\PHPToolkit\Loader;
use Illuminate\Routing\Router;
use Laravel\Scout\EngineManager;

trait InstallAndMigrate
{

	protected function installAndMigrate(): void
	{
		$this->artisan('key:generate');

		$this->runAdminInstallerCommand();

		$this->loadLaravelMigrations();
		$this->artisan('migrate', ['--database' => 'testing']);
		$this->loadMigrationsFrom(database_path('migrations'));
		$this->artisan('migrate', ['--database' => 'testing']);

		Loader::includeAllFilesFromDirRecursive(database_path('seeders'));

		$this->artisan('db:seed', ['--database' => 'testing']);
		$this->artisan('oxygen:seed', ['--database' => 'testing']);

		// add custom middleware
		/** @var Router $router */
		$router = app('Illuminate\Routing\Router');
		$router->aliasMiddleware('auth.acl', \EMedia\Oxygen\Http\Middleware\AuthorizeAcl::class);
	}

	protected function runAdminInstallerCommand(): void
	{
		$this->artisan(
			'oxygen:dashboard:install --name Workbench --dev_url workbench.test --dbname workbench --dbpass root ' .
			'--install_dependencies false --no-interaction'
		);
	}
}
