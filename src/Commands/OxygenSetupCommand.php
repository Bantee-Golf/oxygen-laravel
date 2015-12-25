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
		$this->readSetupConfig();

		// get developer input
		$this->getDeveloperInput();

		$this->saveSetupConfig();

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

		// we don't ask for confirmation on this
		$this->updateKnownStrings();

		// publish assets and other files
		$this->publishFiles();

		// Setup Completed! Show any info to the user.
		$this->showProgressLog();
	}


	/**
	 *	Get user input to customise setup
	 *
	 */
	protected function getDeveloperInput()
	{
		$userInput = [];

		$userInput['projectName'] = $this->anticipate('What is the project name? (REQUIRED)', [], 'Star Wars');
		$userInput['fromEmail']   = $this->ask('What is the `from` email address for system emails?');

		if ( ! $this->confirm('Should the project have Multi-Tenant support?', false) )
			$userInput['multiTenant'] = false;

		$this->projectConfig = array_merge($userInput, $this->projectConfig);
	}

	protected function defaultConfig()
	{
		return [
			'description'	=> 'Setup options for Oxygen assets',
			'multiTenant'	=> true,
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
			$stubMap = [
				[
					'stub' => __DIR__ . '/../../Stubs/Migrations/001_create_tenants_tables.php',
					'path' => database_path('migrations/' . $this->getTimestamp() . '_create_tenants_tables.php'),
					'name' => 'Tenants Migration',
				]
			];
			$this->compileStubs($stubMap);
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

		$this->compileStubs([
			'stub'	=> __DIR__ . '/../../Stubs/Migrations/002_create_invitations_table.php',
			'path'  => database_path('migrations/' . $this->getTimestamp() . '_create_invitations_table.php'),
			'name'	=> 'Invitations Migration'
		]);

		if ($this->projectConfig['multiTenant'])
		{
			$this->compileStubs([
					'stub' => __DIR__ . '/../../Stubs/Migrations/003_update_bouncer_tables.php',
					'path' => database_path('migrations/' . $this->getTimestamp() . '_update_bouncer_tables.php'),
					'name' => 'Update bouncer tables to support multi-tenantcy'
			]);
		}

	}

	protected function getStubMap()
	{
		$stubMap = [
			[
				'stub'	=> __DIR__ . '/../../Stubs/Config/bower.stub',
				'path'  => base_path('bower.json'),
				'name'	=> 'bower.json'
			],
			[
				'stub'	=> __DIR__ . '/../../Stubs/Common/User.php',
				'path'  => app_path('User.php'),
				'name'	=> 'User.php'
			]
		];
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
					'value' => "'auth' => \EMedia\Oxygen\Http\Middleware\Authenticate::class"
				],
				[
					'name'	=> 'middleware',
					'value' => "\EMedia\Oxygen\Http\Middleware\LoadViewSettings::class"
				],
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
		}
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

	protected function updateKnownStrings()
	{
		$fromEmail   = $this->projectConfig['fromEmail'];
		$projectName = $this->projectConfig['projectName'];

		$stringsToReplace = [
			[
				'path'		=> app_path('Http/Middleware/RedirectIfAuthenticated.php'),
				'search'	=> "return redirect('/home');",
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
				'path'		=> app_path('Http/routes.php'),
				'search'	=> "return view('adminPanel::pages.home'",
				'replace'	=> "return view('oxygen::pages.home'"
			],
//			[
//				'path'		=> app_path('Http/Controllers/DashboardController.php'),
//				'search'	=> "adminPanel::dashboard.dashboard",
//				'replace'	=> "oxygen::dashboard.dashboard"
//			]
		];

		foreach ($stringsToReplace as $stringData)
		{
			if ( ! $this->files->exists($stringData['path']) ) {
				$this->error($stringData['path'] . ' not found.');
				continue;
			}
			$this->replaceIn($stringData['path'], $stringData['search'], $stringData['replace']);
		}
	}

	protected function publishFiles()
	{
		$assetInfo = [
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['views'],
				],
				'desc'			=> 'default views (say `no` if you do not intent do modify default views)',
				'default'		=> false
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['source-public-assets'],
				],
				'desc' 			=> 'uncompiled assets (SASS, JS source files etc.)'
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['public-assets'],
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['database-seeds'],
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['common-controllers'],
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['auth-controllers'],
				]
			],
//			[
//				'command'		=> 'vendor:publish',
//				'arguments'		=> [
//					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
//					'--tag'			=> ['auth-middleware'],
//				]
//			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['config'],
				]
			]
		];

		if ($this->confirm('Publish assets?', false))
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

		foreach ($this->progressLog['info'] as $message)
			$this->info($message);
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


}