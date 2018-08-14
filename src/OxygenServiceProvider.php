<?php

namespace EMedia\Oxygen;

use EMedia\Oxygen\Commands\CreateNewUserCommand;
use Illuminate\Support\Facades\Schema;
use EMedia\Oxygen\Commands\OxygenSetupCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Silber\Bouncer\Database\Models;

class OxygenServiceProvider extends ServiceProvider
{

	public function boot()
	{
		// Add support for earlier versions before MySQL 5.7.7
		Schema::defaultStringLength(191);

		// load default views
		$this->loadViewsFrom(__DIR__.'/../resources/views', 'oxygen');

		// allow user to publish views
		$this->publishes([
			__DIR__ . '/../resources/views' => base_path('resources/views/vendor/oxygen'),
		], 'views');

		// SASS files
		$this->publishes([
			__DIR__ . '/../resources/assets/sass' => base_path('resources/assets/sass'),
		], 'source-sass');

		// JS source
		$this->publishes([
			__DIR__ . '/../resources/assets/js' => base_path('resources/assets/js'),
		], 'source-js');

		// public static assets (JS, CSS etc)
		$this->publishes([
			__DIR__ . '/../public_html/js/theme'  => public_path('/js/theme'),
			__DIR__ . '/../public_html/css' => public_path('/css'),
			__DIR__ . '/../public_html/favicon.ico' => public_path('/favicon.ico'),
		], 'public-assets');

		// publish common entities
		$this->publishes([
			__DIR__ . '/../Stubs/Entities' => app_path('Entities'),
		], 'entities');

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
			__DIR__.'/../Stubs/config/oxygen.php' => config_path('oxygen.php')
		], 'oxygen-config');

		$this->registerCustomValidators();

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom( __DIR__ . '/../config/auth.php', 'auth');

		$this->registerDependentServiceProviders();
		$this->registerAliases();

		if ($this->app->environment('local'))
		{
			$this->app->singleton("emedia.oxygen.setup", function () {
				return app(OxygenSetupCommand::class);
			});
			$this->commands("emedia.oxygen.setup");
		}

		$this->commands(CreateNewUserCommand::class);

		Models::setAbilitiesModel(config('auth.abilityModel'));
		Models::setRolesModel(config('auth.roleModel'));
		// Models::setUsersModel(config('auth.model'));
	}

	/**
	 *
	 * Register dependant service providers for the package
	 *
	 */
	private function registerDependentServiceProviders()
	{
		$this->app->register(\EMedia\MultiTenant\MultiTenantServiceProvider::class);
		$this->app->register(\EMedia\Generators\GeneratorServiceProvider::class);
		$this->app->register(\EMedia\Helpers\HelpersServiceProvider::class);
		$this->app->register(\Silber\Bouncer\BouncerServiceProvider::class);
		$this->app->register(\Cviebrock\EloquentSluggable\ServiceProvider::class);
		$this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
		$this->app->register(\EMedia\Render\RenderServiceProvider::class);
		$this->app->register(\Collective\Html\HtmlServiceProvider::class);
	}

	/**
	 *
	 * Register aliases for the package
	 *
	 */
	private function registerAliases()
	{
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();

		$loader->alias('TenantManager', \EMedia\MultiTenant\Facades\TenantManager::class);
		$loader->alias('Bouncer', \Silber\Bouncer\BouncerFacade::class);
		$loader->alias('Debugbar', \Barryvdh\Debugbar\Facade::class);
		$loader->alias('Form', \Collective\Html\FormFacade::class);
		$loader->alias('Render', \EMedia\Render\Facades\RenderFacade::class);
	}

	private function registerCustomValidators()
	{
		// custom validation rules

		// match array count is equal
		// eg: match_count_with:permission::name
		// this will match the array count between both fields
		Validator::extend('match_count_with', function ($attribute, $value, $parameters, $validator) {
			// dd(count($value));
			$otherFieldCount = request()->get($parameters[0]);
			return (count($value) === count($otherFieldCount));
		});

		// custom message
		Validator::replacer('match_count_with', function ($message, $attribute, $rule, $parameters) {
			return "The values given in two array fields don't match.";
		});
	}

}