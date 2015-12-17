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
		if (! $this->app->routesAreCached()) {
			require __DIR__.'/Http/routes.php';
		}

		// allow user to publish views
		$this->publishes([
			__DIR__.'/../resources/views' => base_path('resources/views/vendor/oxygen'),
		], 'views');

//		$this->publishes([
//			__DIR__.'/../resources/views/auth' => base_path('resources/views/auth'),
//		], 'views-auth');
//
//		$this->publishes([
//			__DIR__.'/../resources/views/emails' => base_path('resources/views/emails'),
//		], 'views-auth');

		// publish public assets
		$this->publishes([
			__DIR__.'/../resources/assets' => base_path('resources/assets'),
		], 'public-source');

		$this->publishes([
			__DIR__.'/../public_html/js' => base_path('public_html/js'),
		], 'public-assets');

		$this->publishes([
			__DIR__.'/../public_html/css' => base_path('public_html/css'),
		], 'public-assets') ;

		// publish Auth controllers
		$this->publishes([
			__DIR__.'/../Stubs/Http/Controllers/Auth' => app_path('Http/Controllers/Auth'),
		], 'auth-logic');

		$this->publishes([
			__DIR__.'/../Stubs/Entities/Auth' => app_path('Entities/Auth'),
		], 'auth-logic');

		$this->publishes([
			__DIR__.'/../Stubs/Http/Middleware' => app_path('Http/Middleware'),
		], 'auth-middleware');

		// publish config
		$this->publishes([
			__DIR__.'/../config/' => config_path(),
		], 'config');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$registrations = [
			'OxygenCommonFiles',
		];

		foreach ($registrations as $registration)
		{
			$this->app->singleton("emedia.oxygen.{$registration}Generator", function () use ($registration) {
				return App::make("EMedia\\Oxygen\\Commands\\{$registration}GeneratorCommand");
			});
			$this->commands("emedia.oxygen.{$registration}Generator");
		}

		$this->app->bind('RoleRepository', 	 config('multiTenant.roleRepository'));
		$this->app->bind('TenantRepository', config('multiTenant.tenantRepository'));
	}

}