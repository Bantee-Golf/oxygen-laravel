<?php

namespace EMedia\Oxygen\Commands;

use EMedia\Generators\Commands\CommonFilesGeneratorCommand;
use EMedia\Generators\Parsers\FileEditor;

class OxygenCommonFilesGeneratorCommand extends CommonFilesGeneratorCommand
{

	protected $signature = 'oxygen:setup';
	protected $description = 'Generate common files for the Oxygen project';

	protected function getStubMap()
	{
		$stubMap = [
			[
				'stub'	=> __DIR__ . '/../../Stubs/Migrations/001_create_tenant_groups_tables.php',
				'path'  => database_path('migrations/' . $this->getTimestamp() . '_create_tenant_groups_tables.php'),
				'name'	=> 'TenantGroups Migration'
			],
			[
				'stub'	=> __DIR__ . '/../../Stubs/Migrations/002_create_invitations_table.php',
				'path'  => database_path('migrations/' . $this->getTimestamp() . '_create_invitations_table.php'),
				'name'	=> 'Invitations Migration'
			],
			[
				'stub'	=> __DIR__ . '/../../Stubs/Config/bower.stub',
				'path'  => base_path('bower.json'),
				'name'	=> 'bower.json'
			]
		];
		return $stubMap;
	}

	public function fire()
	{
		// generate the files from stubs
		$this->compileStubs();

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
	}

	public function compileStubs()
	{
		$stubMap = $this->getStubMap();

		foreach ($stubMap as $stubData)
		{
			$name = $this->parseName($stubData['name']);

			if ($this->confirm('Create ' . $stubData['path'] . '?', true))
			{

				if ($this->files->exists($stubData['path']))
				{
					$this->error($stubData['name'] . ' already exists.');

					continue;
				}

				$this->makeDirectory($stubData['path']);

				// parse each file and write
				$this->files->put($stubData['path'], $this->buildClass($name, $stubData['stub']));

				$this->info($stubData['name'] . ' created successfully.');
			}
		}
	}

	protected function updateMiddleware()
	{
		if ($this->confirm('Update middleware?', true))
		{
			$editor = new FileEditor();
			$inputFile = app_path('Http/Kernel.php');

			$fields = [
				[
					'name'	=> 'middleware',
					'value' => "\App\Http\Middleware\LoadViewSettings::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'auth.acl' => \App\Http\Middleware\AuthenticateAcl::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'auth.api' => \App\Http\Middleware\ApiAuthenticate::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'role' => Zizaco\Entrust\Middleware\EntrustRole::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'permission' => Zizaco\Entrust\Middleware\EntrustPermission::class"
				],
				[
					'name'	=> 'routeMiddleware',
					'value' => "'ability' => Zizaco\Entrust\Middleware\EntrustAbility::class"
				]
			];

			if ($editor->addPropertyValuesToFile($inputFile, $fields))
			{
				$this->info('Middleware updated.');
			}
		}
	}

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
		if ($this->confirm("Update routes file with routes for tenants, invitations, teams?", true))
		{
			$routesStub = $this->files->get(__DIR__ . '/../../Stubs/Http/routes.stub');
			$routesFilePath = app_path('Http/routes.php');
			$result = $this->files->append($routesFilePath, $routesStub);
			if ($result) $this->info('Routes file updated.');
		}
	}

	protected function updateKnownStrings()
	{
		$stringsToReplace = [
			[
				'path'		=> app_path('Http/Middleware/RedirectIfAuthenticated.php'),
				'search'	=> "return redirect('/home');",
				'replace'	=> "return redirect('/dashboard');"
			],
			[
				'path'		=> config_path('mail.php'),
				'search'	=> "'from' => ['address' => null, 'name' => null],",
				'replace'	=> "'from' => ['address' => 'shane7@gmail.com', 'name' => 'Shane (Dev)'],"
			]
		];

		foreach ($stringsToReplace as $stringData)
		{
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
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['public-source'],
				]
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
					'--tag'			=> ['auth-logic'],
				]
			],
			[
				'command'		=> 'vendor:publish',
				'arguments'		=> [
					'--provider'	=> 'EMedia\Oxygen\OxygenServiceProvider',
					'--tag'			=> ['auth-middleware'],
				]
			],
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
				if ($this->confirm('Publish ' . $asset['arguments']['--tag'][0] . '?', true))
				{
					$this->call($asset['command'], $asset['arguments']);
				}
			}
		}
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

	protected function getTimestamp()
	{
		// get microtime of file generation to preserve migration sequence
		$time = explode(" ",microtime());

		// change the format to 2008_07_14_010813.982
		return date("Y_m_d_His", $time[1]) . '.' .substr((string)$time[0], 2, 3);
	}

	/**
	 * Replace the given string in the given file.
	 *
	 * @param  string  $path
	 * @param  string|array  $search
	 * @param  string|array  $replace
	 * @return void
	 */
	protected function replaceIn($path, $search, $replace)
	{
		$this->files->put($path, str_replace($search, $replace, $this->files->get($path)));
	}



}