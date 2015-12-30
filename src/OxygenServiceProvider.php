<?php

namespace EMedia\Oxygen;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class OxygenServiceProvider extends ServiceProvider
{

	public function boot()
	{
		// load default views
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'oxygen');

		// register routes
		// if (! $this->app->routesAreCached()) require __DIR__.'/Http/routes.php';

		// allow user to publish views
		$this->publishes([
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen'),
		], 'views');

		// SASS files
		$this->publishes([
			__DIR__ . '/../resources/assets/sass' => base_path('resources/assets/sass'),
		], 'source-sass');

		// public static assets (JS, CSS etc)
		$this->publishes([
			__DIR__ . '/../public_html/js/theme'  => public_path('/js/theme'),
			__DIR__ . '/../public_html/css' => public_path('/css'),
			__DIR__ . '/../public_html/favicon.ico' => public_path('/favicon.ico'),
		], 'public-assets');

		// angular app source
		$this->publishes([
			__DIR__ . '/../resources/assets/js' => base_path('resources/assets/js'),
			__DIR__ . '/../public_html/js' 		=> public_path('/js'),
		], 'angular-source');

		// publish common controllers
		$this->publishes([
				__DIR__ . '/../Stubs/Http/Controllers/Common' => app_path('Http/Controllers'),
		], 'common-controllers');

		// publish Auth controllers
		$this->publishes([
			__DIR__ . '/../Stubs/Http/Controllers/Auth' => app_path('Http/Controllers/Auth'),
		], 'auth-controllers');

		$this->publishes([
				__DIR__ . '/../Stubs/Seeds' => database_path('seeds'),
		], 'database-seeds');

		// publish config
		$this->publishes([
				__DIR__.'/../config/settings.php' => config_path('settings.php')
		], 'config');

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom( __DIR__ . '/../config/auth.php', 'auth');

		if ($this->app->environment() == 'local')
		{
			$this->app->singleton("emedia.oxygen.setup", function () {
				return app("EMedia\\Oxygen\\Commands\\OxygenSetupCommand");
			});
			$this->commands("emedia.oxygen.setup");
		}

		$this->app->bind('RoleRepository', 	 config('auth.roleRepository'));
		$this->app->bind('TenantRepository', config('auth.tenantRepository'));
	}

}