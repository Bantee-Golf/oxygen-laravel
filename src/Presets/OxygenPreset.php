<?php
namespace EMedia\Oxygen\Presets;


use Illuminate\Support\Facades\File;
use Laravel\Ui\Presets\Preset;

class OxygenPreset extends Preset
{

	/**
	 *
	 * Start the installer
	 *
	 */
	public static function install(): void
	{
		// add npm packages
		self::updatePackages();
		
		// add JS files
		self::updateScripts();
		
		// add SCSS files
		self::updateStyles();
	}

	/**
	 *
	 * Update NPM dependencies
	 *
	 * @param array $packages
	 * @return array
	 */
	protected static function updatePackageArray(array $packages): array
	{
		return array_merge([
			'@fortawesome/fontawesome-free' => '^5.13.0',
			'bootstrap' => '^4.4.1',
			'jquery' => '^3.4.1',
			'jquery-validation' => '^1.19.1',
			'popper.js' => '^1.16.1',
			'select2' => '^4.0.13',
			'typeahead.js' => '^0.11.1',
            "dropzone" =>  "^5.7.0"
		], $packages);
	}

	/**
	 *
	 * Update JS files
	 *
	 */
	protected static function updateScripts(): void
	{
		File::delete(base_path('resources/js/app.js'));
		File::delete(base_path('resources/js/bootstrap.js'));
		File::copyDirectory(__DIR__ . '/../../resources/js', base_path('resources/js'));
	}

	/**
	 *
	 * Update CSS styles
	 *
	 */
	private static function updateStyles(): void
	{
		File::copyDirectory(__DIR__ . '/../../resources/sass', base_path('resources/sass'));
	}

}