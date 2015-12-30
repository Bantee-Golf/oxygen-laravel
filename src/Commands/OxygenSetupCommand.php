<?php

namespace EMedia\Oxygen\Commands;

use EMedia\Generators\Commands\BaseGeneratorCommand;
use EMedia\Generators\Parsers\FileEditor;
use Illuminate\Filesystem\Filesystem;

class OxygenSetupCommand extends BaseGeneratorCommand
{

	protected $signature   = 'oxygen:setup';
	protected $description = 'Generate common files for the Oxygen project';
	protected $projectConfig = [];

	protected $progressLog = [
		'info' 		=> [],
		'errors' 	=> [],
	];

	protected $configFile;

	public function __construct(Filesystem $files)
	{
		parent::__construct($files);

		$this->projectConfig = $this->defaultConfig();
		$this->configFile = base_path('oxygen.json');
	}


	public function fire()
	{
		// $this->readSetupConfig();

		// get developer input
		$this->getDeveloperInput();

		$this->generateGeneratorPackageFiles();

		// generate the migrations
		$this->generateMigrations();

		// generate the files from stubs
		$this->compileStubs($this->getStubMap());

		// update routes
		$this->addGenericRoutes();

		// update middleware
		$this->updateMiddleware();

		// update app service providers
		// $this->updateServiceProviders();

		// publish assets and other files
		$this->publishFiles();

		// we don't ask for confirmation on this
		$this->replaceKnownStrings();

		// Setup Completed! Show any info to the user.
		$this->showProgressLog();

		// $this->saveSetupConfig();
	}


	/**
	 *	Get user input to customise setup
	 *
	 */
	protected function getDeveloperInput()
	{
		$userInput = [];

		$userInput['projectName'] 	 = $this->ask('What is the project name?');
		$userInput['fromEmail']   	 = $this->ask('What is the `from` email address for system emails?');
		$userInput['seedAdminEmail'] = $this->anticipate('What is your email to seed the database?', [], $userInput['fromEmail']);
		$userInput['dashboardType']  = $this->choice('What should be the type of the dashboard?', ['HTML/CSS (Default)', 'Angular'], 0);

		if ($this->confirm('Should the project have Multi-Tenant support?', false))
			$userInput['multiTenant'] = true;

		$this->projectConfig = array_merge($this->projectConfig, $userInput);
	}

	protected function defaultConfig()
	{
		return [
			'description'	=> 'Setup options for Oxygen assets',
			'multiTenant'	=> false,
		];
	}

	protected function readSetupConfig()
	{
		if ( ! file_exists($this->configFile)) return;

		$configContents = file_get_contents($this->configFile);
		$mergedConfig   = array_merge(json_decode($configContents, true), $this->projectConfig);
		$this->projectConfig = $mergedConfig;
	}

	protected function saveSetupConfig()
	{
		file_put_contents($this->configFile, json_encode($this->projectConfig, JSON_PRETTY_PRINT));
	}


	protected function generateGeneratorPackageFiles()
	{
		$this->call('scaffold:common:config');
		$this->call('scaffold:common');
	}

	protected function generateMigrations()
	{
		// migration order
		// tenants
		// bouncer
		// bouncer updates
		// invitations

		if ($this->projectConfig['multiTenant'])
		{
			// publish tenants
			$this->compileStubs([
				[
					'stub' => __DIR__ . '/../../Stubs/Migrations/001_create_tenants_tables.php',
					'path' => database_path('migrations/' . $this->getTimestamp() . '_create_tenants_tables.php'),
					'name' => 'Tenants Migration',
				]
			]);
		}

		// bouncer migration (for roles and ACL)
		if ($this->confirm('Create bouncer migrations?', true))
		{
			$bouncerPublishCommand = [
				'command' => 'vendor:publish',
				'arguments' => [
					'--provider' => 'Silber\Bouncer\BouncerServiceProvider',
					'--tag' => ['migrations'],
				]
			];
			$this->call($bouncerPublishCommand['command'], $bouncerPublishCommand['arguments']);
		}

		$stubMap = [
			[
				'stub'	=> __DIR__ . '/../../Stubs/Migrations/002_create_invitations_table.php',
				'path'  => database_path('migrations/' . $this->getTimestamp() . '_create_invitations_table.php'),
				'name'	=> 'Invitations Migration'
			],
			[
				'stub'	=> __DIR__ . '/../../Stubs/Migrations/003_update_bouncer_tables_common.php',
				'path'  => database_path('migrations/' . $this->getTimestamp() . '_update_bouncer_tables_common.php'),
				'name'	=> 'Add new columns to Bouncer tables'
			]
		];
		$this->compileStubs($stubMap);

		if ($this->projectConfig['multiTenant'])
		{
			$this->compileStubs([[
				'stub' => __DIR__ . '/../../Stubs/Migrations/004_update_bouncer_tables_multi_tenant.php',
				'path' => database_path('migrations/' . $this->getTimestamp() . '_update_bouncer_tables_multi_tenant.php'),
				'name' => 'Update bouncer tables to support multi-tenancy'
			]]);
		}

	}

	protected function getStubMap()
	{
		$stubMap = [];

		$stub = [
			'path'	=> base_path('bower.json'),
			'name'	=> 'Bower config',
		];

		if ($this->projectConfig['dashboardType'] == 'Angular') {
			$stub['stub'] = __DIR__ . '/../../Stubs/ProjectConfig/bower-angular.json';
		} else {
			$stub['stub'] = __DIR__ . '/../../Stubs/ProjectConfig/bower-html.json';
		}
		$stubMap[] = $stub;

		if ($this->projectConfig['multiTenant']) {
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/Common/User.MultiTenant.php',
				'path'  => app_path('User.php'),
				'name'	=> 'User.php'
			];
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/config/oxygen-multiTenant.php',
				'path'  => config_path('oxygen.php'),
				'name'	=> 'Multi-tenant configuration'
			];
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/config/acl-multiTenant.php',
				'path'  => config_path('acl.php'),
				'name'	=> 'ACL for multi-tenant configuration'
			];
		} else {
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/Common/User.SingleTenant.php',
				'path'  => app_path('User.php'),
				'name'	=> 'User.php'
			];
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/config/oxygen-singleTenant.php',
				'path'  => config_path('oxygen.php'),
				'name'	=> 'Single-tenant configuration'
			];
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/config/acl-singleTenant.php',
				'path'  => config_path('acl.php'),
				'name'	=> 'ACL for single-tenant configuration'
			];
		}

		$this->progressLog['info'][] = 'Bower installation should be completed. Run `bower install`.';

		return $stubMap;
	}



	protected function updateMiddleware()
	{
		if ($this->confirm('Update Kernel.php with new middleware?', true))
		{
			$editor = new FileEditor();
			$inputFile = app_path('Http/Kernel.php');

			$fields = [
				[
					'name'	=> 'routeMiddleware',
					'value' => "'auth.acl' => \EMedia\Oxygen\Http\Middleware\AuthenticateAcl::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'auth.api' => \EMedia\Oxygen\Http\Middleware\ApiAuthenticate::class"
				]
			];

			if ($editor->addPropertyValuesToFile($inputFile, $fields))
			{
				$this->info('Middleware updated.');
				$this->progressLog['info'][] = 'Middleware updated. Check `Http\Kernel.php` for duplicate entries.';
			}

			$fields = [
				[
					'name'	=> 'web',
					'value' => "\EMedia\Oxygen\Http\Middleware\LoadViewSettings::class"
				],
			];
			$editor->addArrayValuesToFile($inputFile, $fields);
			$this->replaceIn($inputFile,
				"'auth' => \App\Http\Middleware\Authenticate::class",
				"'auth' => \EMedia\Oxygen\Http\Middleware\Authenticate::class");
		}
	}


	protected function addGenericRoutes()
	{
		// ask the user and update the routes file if required
		if ($this->confirm("Update routes file with routes for auth, invitations, roles?", true))
		{
			$routesStub = $this->files->get(__DIR__ . '/../../Stubs/Http/routes.stub');
			$routesFilePath = app_path('Http/routes.php');
			$result = $this->files->append($routesFilePath, $routesStub);
			if ($result)
			{
				$this->info('Routes file updated.');
				$this->progressLog['info'][] = 'Routes updated. Check `Http\routes.php` for duplicate entries.';
			}
		}
	}

	protected function replaceKnownStrings()
	{
		$fromEmail   = $this->projectConfig['fromEmail'];
		$projectName = $this->projectConfig['projectName'];

		$stringsToReplace = [
			[
				'path'		=> app_path('Http/Middleware/RedirectIfAuthenticated.php'),
				'search'	=> "return redirect('/');",
				'replace'	=> "return redirect('/dashboard');"
			],
			[
				'path'		=> config_path('mail.php'),
				'search'	=> "'from' => ['address' => null, 'name' => null],",
				'replace'	=> "'from' => ['address' => '$fromEmail', 'name' => '$projectName (Dev)'],"
			],
			[
				'path'		=> config_path('settings.php'),
				'search'	=> "Application Admin Panel",
				'replace'	=> "$projectName"
			],
			[
				'path'		=> config_path('auth.php'),
				'search'	=> "auth.emails.password",
				'replace'	=> "oxygen::emails.password"
			],
			[
				'path'		=> database_path('seeds/UsersTableSeeder.php'),
				'search'	=> "shane.emedia@gmail.com",
				'replace'	=> $this->projectConfig['seedAdminEmail']
			]
		];

		if ($this->projectConfig['dashboardType'] == 'Angular') {
			$stringsToReplace[] = [
				'path'		=> app_path('Http/Controllers/DashboardController.php'),
				'search'	=> "return view('oxygen::dashboard.dashboard'",
				'replace'	=> "return view('oxygen::dashboard.dashboard-angular'"
			];
		};

		foreach ($stringsToReplace as $stringData)
		{
			$this->replaceIn($stringData['path'], $stringData['search'], $stringData['replace']);
		}
	}

	protected function replaceIn($path, $search, $replace)
	{
		if ( ! $this->files->exists($path) ) {
			$this->error($path . ' not found.');
			$this->progressLog['errors'][] = $path . ' not found to update.';
			return false;
		}
		parent::replaceIn($path, $search, $replace);
	}

	protected function publishFiles()
	{
		$assetInfo = [
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['views'],
					'--force'		=> true,
				],
				'desc'			=> 'default views (say `no` if you do not intent do modify default views)',
				'default'		=> false
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['source-sass'],
					'--force'		=> false,
				],
				'desc' 			=> 'uncompiled SASS files'
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['public-assets'],
					'--force'		=> true,
				],
				'desc'			=> 'JS, CSS and other assets in public folder'
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['database-seeds'],
					'--force'		=> true,
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['common-controllers'],
					'--force'		=> true,
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['auth-controllers'],
					'--force'		=> true,
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['config'],
					'--force'		=> true,
				]
			]
		];

		if ($this->projectConfig['dashboardType'] == 'Angular') {
			$assetInfo[] = [
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['angular-source'],
				]
			];
		}

		if ($this->confirm('Publish project assets?', true))
		{
			foreach ($assetInfo as $asset)
			{
				$default = (empty($asset['default']))? true: $asset['default'];
				$desc    = (empty($asset['desc']))? $asset['arguments']['--tag'][0]: $asset['desc'];

				if ($this->confirm("Publish $desc ?", $default))
					$this->call($asset['command'], $asset['arguments']);
			}
		}


	}

	protected function showProgressLog()
	{
		$this->info('*** SETUP COMPLETED! ***');
		$this->info('');

		foreach ($this->progressLog['info'] as $message)
			$this->info($message);

		if (count($this->progressLog['errors'])) {
			$this->error('ERROR SUMMARY:');
			foreach ($this->progressLog['errors'] as $message)
				$this->error($message);
		}
	}

	protected function buildClass($name, $stubPath = null)
	{
		if (!$stubPath) $stubPath = $this->getStub();

		$stub = $this->files->get($stubPath);
		$this->replaceNamespace($stub, $name);

		return $stub;
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return '';
	}

	// DEPRECATED
	private function updateServiceProviders()
	{
		if ($this->confirm('Update service providers in app.php?', true))
		{
			$editor = new FileEditor();
			$inputFile = config_path('app.php');

			$fields = [
					[
							'name'	=> 'providers',
							'value' => '// Oxygen Support Providers '
					],
					[
							'name'	=> 'providers',
							'value' => "EMedia\MultiTenant\MultiTenantServiceProvider::class"
					],
					[
							'name'	=> 'providers',
							'value' => "EMedia\Generators\GeneratorServiceProvider::class"
					],
					[
							'name'	=> 'providers',
							'value' => "EMedia\MediaManager\MediaManagerServiceProvider::class"
					],
					[
							'name'	=> 'providers',
							'value' => "EMedia\Oxygen\OxygenServiceProvider::class"
					],
					[
							'name'	=> 'aliases',
							'value' => '// Oxygen Aliases '
					],
					[
							'name'	=> 'aliases',
							'value' => "'TenantManager' => EMedia\MultiTenant\Facades\TenantManager::class"
					],
					[
							'name'	=> 'aliases',
							'value' => "'FileHandler'   => EMedia\MediaManager\Facades\FileHandler::class"
					],
					[
							'name'	=> 'aliases',
							'value' => "'ImageHandler'  => EMedia\MediaManager\Facades\ImageHandler::class"
					]
			];

			if ($editor->addPropertyValuesToFile($inputFile, $fields, config_path('appnew.php')))
			{
				$this->info('Service Providers updated.');
			}
		}
	}


}