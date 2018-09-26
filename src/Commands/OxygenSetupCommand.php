<?php

namespace EMedia\Oxygen\Commands;

use EMedia\Generators\Commands\BaseGeneratorCommand;
use EMedia\Generators\Parsers\FileEditor;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class OxygenSetupCommand extends BaseGeneratorCommand
{

	protected $signature   = 'setup:oxygen-project';
	protected $description = 'Generate common files for the Oxygen project';
	protected $projectConfig = [];

	protected $progressLog = [
		'info' 		=> [],
		'errors' 	=> [],
		'comments'	=> [],
		'instructions' => [],
		'files' => [],
	];

	protected $configFile;

	public function __construct(Filesystem $files)
	{
		parent::__construct($files);

		$this->projectConfig = $this->defaultConfig();
		$this->configFile = base_path('oxygen.json');
	}

	/**
	 *
	 * Laravel 5.6+ support
	 *
	 */
	public function handle()
	{
		return $this->fire();
	}

	public function fire()
	{
		// $this->readSetupConfig();

		// get developer input
		$this->getDeveloperInput();

		// generate the migrations
		$this->generateMigrations();

		// generate the files from stubs
		$this->compileStubs($this->getStubMap());

		// update routes
		$this->addGenericRoutes();

		// update middleware
		$this->updateMiddleware();

		// publish assets and other files
		$this->publishFiles();

		// we don't ask for confirmation on this
		$this->replaceKnownStrings();

		$this->progressLog['instructions'][] = ['npm install', 'Install NPM packages. Node.js must be installed on your machine. Check if installed with `npm -v`'];
		$this->progressLog['instructions'][] = ['npm run dev', 'Compile webpack and build the application.'];
		$this->progressLog['instructions'][] = ['npm run watch', 'Run and watch the application on browser (Does NOT work with Homestead)'];
		$this->progressLog['instructions'][] = ['php artisan serve', 'Run the local test server'];

		// Setup Completed! Show any info to the user.
		$this->showProgressLog();

		$this->updateReadMeFile();

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
		$userInput['fromEmail']   	 = $this->ask('What is the `from` email address for system emails?', 'info@elegantmedia.com.au');
		$userInput['seedAdminEmail'] = $this->anticipate('What is your email to seed the database?', [], $userInput['fromEmail']);
		$userInput['devMachineUrl'] = $this->anticipate('What is the local development URL?', [], 'localhost.dev');
		// $userInput['dashboardType']  = $this->choice('What should be the type of the dashboard?', ['HTML/CSS (Default)', 'Angular'], 0);

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

		$stubMap = [
			[
				'stub'	=> __DIR__ . '/../../Stubs/Migrations/002_create_role_permission_tables.php',
				'path'  => database_path('migrations/' . $this->getTimestamp() . '_create_role_permission_tables.php'),
				'name'	=> 'Add new columns to Bouncer tables'
			],
			[
				'stub'	=> __DIR__ . '/../../Stubs/Migrations/003_create_invitations_table.php',
				'path'  => database_path('migrations/' . $this->getTimestamp() . '_create_invitations_table.php'),
				'name'	=> 'Invitations Migration'
			],
		];
		$this->compileStubs($stubMap);

		if ($this->projectConfig['multiTenant'])
		{
			$this->compileStubs([[
				'stub' => __DIR__ . '/../../Stubs/Migrations/004_update_auth_tables_multi_tenant.php',
				'path' => database_path('migrations/' . $this->getTimestamp() . '_update_auth_tables_multi_tenant.php'),
				'name' => 'Update bouncer tables to support multi-tenancy'
			]]);
		}

	}

	protected function getStubMap()
	{
		$stubMap = [];

		$stub = [
			'path'	=> base_path('bower.json'),
			'name'	=> 'bower.json',
		];
		$stub['stub'] = __DIR__ . '/../../Stubs/ProjectConfig/bower.json';
		$stubMap[] = $stub;

		$stub = [
			'path'	=> base_path('.bowerrc'),
			'name'	=> 'Bower config (.bowerrc)',
		];
		$stub['stub'] = __DIR__ . '/../../Stubs/ProjectConfig/bowerrc.stub';
		$stubMap[] = $stub;

		$stub = [
			'path'	=> base_path('webpack.mix.js'),
			'name'	=> 'webpack.mix.js',
		];
		$stub['stub'] = __DIR__ . '/../../Stubs/ProjectConfig/webpack.mix.js';
		$stubMap[] = $stub;

		$stub = [
			'path'	=> base_path('apidoc.json'),
			'name'	=> 'APIDoc Configuration (apidoc.json)',
		];
		$stub['stub'] = __DIR__ . '/../../Stubs/ProjectConfig/apidoc.json';
		$stubMap[] = $stub;

		$stubMap[] = [
			'stub'	=> __DIR__ . '/../../Stubs/config/oxygen.php',
			'path'  => config_path('oxygen.php'),
			'name'	=> 'Oxygen Configuration'
		];

		if ($this->projectConfig['multiTenant']) {
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/Common/User.MultiTenant.php',
				'path'  => app_path('User.php'),
				'name'	=> 'User.php'
			];
			/*$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/config/acl-multiTenant.php',
				'path'  => config_path('acl.php'),
				'name'	=> 'ACL for multi-tenant configuration'
			];*/
		} else {
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/Common/User.SingleTenant.php',
				'path'  => app_path('User.php'),
				'name'	=> 'User.php'
			];
			/*
			$stubMap[] = [
				'stub'	=> __DIR__ . '/../../Stubs/config/acl-singleTenant.php',
				'path'  => config_path('acl.php'),
				'name'	=> 'ACL for single-tenant configuration'
			];*/
		}

		$this->progressLog['instructions'][] = ['bower install', 'Install bower dependencies. Check if Bower is installed with `bower -v`'];

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
					'value' => "'auth.acl' => \EMedia\Oxygen\Http\Middleware\AuthorizeAcl::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'auth.api' => \EMedia\Oxygen\Http\Middleware\ApiAuthenticate::class"
				]
			];

			if ($editor->addPropertyValuesToFile($inputFile, $fields))
			{
				$this->info('Middleware updated.');
				$this->progressLog['files'][] = ['Http\Kernel.php', 'Check for duplicate entries.'];
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


	/**
	 *
	 * Update the Routes file
	 *
	 * @return bool
	 * @throws FileNotFoundException
	 */
	protected function addGenericRoutes()
	{
		$routesFilePath = base_path('routes/web.php');
		$addAgain = false;

		try {
			// check if the routes file mentions anything about the 'oxygen routes'
			// if so, it might already be there. Ask the user to confirm.
			if ($this->isTextInFile($routesFilePath, 'Oxygen Routes', false)) {
				if (!$this->confirm("Oxygen routes are already in routes file. Add again?", false)) {
					return false;
				}
				$addAgain = true;
			}
		} catch (FileNotFoundException $ex) {
			$this->error("Routes file not found at `{$routesFilePath}`. Skipping adding routes...");
		}

		// ask the user and update the routes file if required
		if ($addAgain || $this->confirm("Update routes file with routes for auth, invitations, roles?", true))
		{
			$routesStub = $this->files->get(__DIR__ . '/../../Stubs/routes/web.stub');

			$result = $this->files->append($routesFilePath, $routesStub);
			if ($result)
			{
				$this->info('Routes file updated.');
				$this->progressLog['files'][] = ['routes\web.php', 'Check for duplicate entries.'];
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
				'search'	=> "return redirect('/home');",
				'replace'	=> "return redirect('/dashboard');"
			],
			[
				'path'		=> config_path('mail.php'),
				'search'	=> "'from' => [
        'address' => 'hello@example.com',
        'name' => 'Example',
    ]",
				'replace'	=> "'from' => ['address' => '$fromEmail', 'name' => '$projectName (Dev)']"
			],
			[
				'path'		=> config_path('app.php'),
				'search'	=> "'name' => 'Laravel',",
				'replace'	=> "'name' => '{$projectName}',"
			],
			[
				'path'		=> database_path('seeds/Auth/UsersTableSeeder.php'),
				'search'	=> "app@elegantmedia.com.au",
				'replace'	=> $this->projectConfig['seedAdminEmail']
			],
			[
				'path'		=> base_path('webpack.mix.js'),
				'search'	=> "localhost.dev",
				'replace'	=> $this->projectConfig['devMachineUrl'],
			],
		];

		if ($this->projectConfig['multiTenant']) {
			$stringsToReplace[] = [
				'path'		=> config_path('oxygen.php'),
				'search'	=> "'multiTenantActive' => false,",
				'replace'	=> "'multiTenantActive' => true,"
			];
			$stringsToReplace[] = [
				'path'		=> app_path('Entities/Auth/Ability.php'),
				'search'	=> "use EMedia\Oxygen\Entities\Auth\SingleTenant\Ability as AbilityBase;",
				'replace'	=> "use EMedia\Oxygen\Entities\Auth\MultiTenant\Ability as AbilityBase;"
			];
			$stringsToReplace[] = [
				'path'		=> app_path('Entities/Auth/Role.php'),
				'search'	=> "use EMedia\Oxygen\Entities\Auth\SingleTenant\Role as BaseRole;",
				'replace'	=> "use EMedia\Oxygen\Entities\Auth\MultiTenant\Role as BaseRole;"
			];
		}

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
					'--tag'			=> ['source-js'],
					'--force'		=> true,
				],
				'desc'			=> 'JS Source Files'
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
					'--tag'			=> ['entities'],
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
					'--tag'			=> ['oxygen-config'],
					'--force'		=> true,
				]
			]
		];

		/*if ($this->projectConfig['dashboardType'] == 'Angular') {
			$assetInfo[] = [
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['angular-source'],
				]
			];
		}*/

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
		$this->info('');
		$this->info('***** OXYGEN SETUP COMPLETED! *****');
		$this->info('');

		if (count($this->progressLog['instructions'])) {
			$this->info('Run these commands in order to complete the build process.');

			$headers = ['ID', 'CLI Command', 'What it does'];

			$rows = [];
			for ($i = 0, $iMax = count($this->progressLog['instructions']); $i < $iMax; $i++) {
				$rows[] = array_merge([$i + 1], $this->progressLog['instructions'][$i]);
			}

			$this->table($headers, $rows);
			$this->info('');
		}

		if (count($this->progressLog['files'])) {
			$this->info('Check these files for accuracy.');

			$headers = ['File', 'What you should check'];
			$this->table($headers, $this->progressLog['files']);
		}

		foreach ($this->progressLog['info'] as $message)
			$this->info($message);

		if (count($this->progressLog['errors'])) {
			$this->error('ERROR SUMMARY:');
			foreach ($this->progressLog['errors'] as $message)
				$this->error($message);
		}
	}

	protected function updateReadMeFile()
	{
		if (count($this->progressLog['instructions'])) {
			$title = '## Local Development Setup Instructions';
			$filePath = base_path('readme.md');

			try {
				if ($this->isTextInFile($filePath, $title)) return false;
			} catch (FileNotFoundException $ex) {
				$this->error('Readme.md file not found at ' . $filePath);
				return false;
			}

			$lines = [];
			$lines[] = "\r";
			$lines[] = $title;
			$lines[] = " ";

			for ($i = 0, $iMax = count($this->progressLog['instructions']); $i < $iMax; $i++) {
				$instruction = $this->progressLog['instructions'][$i];
				if (count($instruction) === 2) {
					$lines[] = "- `{$instruction[0]}` - {$instruction[1]}";
				} else {
					$lines[] = "- " . $instruction[0];
				}
			}

			$content = implode("\r\n", $lines);

			$result = $this->files->append($filePath, $content);

			$this->info("Readme.md file updated with build instructions.");
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


	/**
	 *
	 * Check if a string exists in a file. (Don't use to check on large files)
	 *
	 * @param      $filePath
	 * @param      $string
	 * @param bool $caseSensitive
	 *
	 * @return bool
	 * @throws FileNotFoundException
	 */
	protected function isTextInFile($filePath, $string, $caseSensitive = true)
	{
		if (!file_exists($filePath)) throw new FileNotFoundException("File $filePath not found");

		$command = ($caseSensitive)? 'strpos': 'stripos';

		return $command(file_get_contents($filePath), $string) !== false;
	}

}