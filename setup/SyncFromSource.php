<?php

use EMedia\LaravelTestbench\Repo\Laravel\Base;
use EMedia\LaravelTestbench\Repo\Laravel\UI;
use EMedia\LaravelTestbench\Repo\RepoCopy;

require_once __DIR__ . '/../vendor/autoload.php';

// https://raw.githubusercontent.com/laravel/laravel/7.x/README.md
// https://raw.githubusercontent.com/laravel/laravel/master/README.md

$repos = [
	[
		'branch' => 'https://raw.githubusercontent.com/laravel/laravel/master/',
		'local_dir' => __DIR__.'/../laravel/laravel/',
		'files' => [
			'app/Models/User.php',
			'app/Http/Kernel.php',
			'app/Http/Controllers/Controller.php',

			'app/Providers/AppServiceProvider.php',
			'app/Providers/AuthServiceProvider.php',
			'app/Providers/EventServiceProvider.php',
			'app/Providers/RouteServiceProvider.php',

			'database/seeders/DatabaseSeeder.php',
			'public/.htaccess',
			'resources/lang/en/auth.php',

			'config/app.php',
			'config/auth.php',

			'routes/web.php',
			'routes/api.php',

			'.env.example',
			'README.md',
			'webpack.mix.js',
		]
	],
	// [
	// 	'branch' => 'https://raw.githubusercontent.com/laravel/jetstream/1.x/',
	// 	'local_dir' => __DIR__.'/../laravel/jetstream/',
	// 	'files' => [
	// 		'stubs/app/Providers/JetstreamServiceProvider.php',
	// 	]
	// ],
];

foreach ($repos as $repo) {
	$localDir = $repo['local_dir'];

	// easier to delete the directory, so we're not left with ghost files from the past
	\ElegantMedia\PHPToolkit\Dir::deleteDirectory($localDir);

	foreach($repo['files'] as $file) {
		$sourceUrl = $repo['branch'].$file;

		if (!$content = file_get_contents($sourceUrl)) {
			throw new \ElegantMedia\OxygenFoundation\Support\Exceptions\FileInvalidException("Failed to fetch from {$sourceUrl}");
		}

		$destination = $localDir.$file;

		\ElegantMedia\PHPToolkit\Dir::makeDirectoryIfNotExists(dirname($destination));

		file_put_contents($destination, $content);
	}

}


//
//// TODO: Add these files
//// database/seeders/DatabaseSeeder.php
//
//$repos2 = [
//    new Base([
//        'README.md',
//        'webpack.mix.js',
//        'app/User.php',
//        'app/Http/Kernel.php',
//        'app/Http/Controllers/Controller.php'
//    ]),
//    new UI([
//        // 'app/Http/Controllers/Auth/ConfirmPasswordController.php',
//        'app/Http/Controllers/Auth/ForgotPasswordController.php',
//        'app/Http/Controllers/Auth/LoginController.php',
//        'app/Http/Controllers/Auth/RegisteredUserController.php',
//        'app/Http/Controllers/Auth/ResetPasswordController.php',
//        'app/Http/Controllers/Auth/VerificationController.php',
//    ])
//];
//
//$copy = new RepoCopy(__DIR__ . '/../LaravelDefaultFiles/');
//
//foreach ($repos as $repo) {
//    $copy->get($repo);
//}
