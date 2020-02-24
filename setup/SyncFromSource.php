<?php

$baseUrl = 'https://raw.githubusercontent.com/laravel/laravel/master/';
$destination = __DIR__ . '/../LaravelDefaultFiles/';

$syncFromSource = [
	'readme.md',
	'webpack.mix.js',
	'app/User.php',
	'app/Http/Kernel.php',
	'app/Http/Controllers/Controller.php',
	// 'app/Http/Controllers/Auth/ConfirmPasswordController.php',
	'app/Http/Controllers/Auth/ForgotPasswordController.php',
	'app/Http/Controllers/Auth/LoginController.php',
	'app/Http/Controllers/Auth/RegisterController.php',
	'app/Http/Controllers/Auth/ResetPasswordController.php',
	'app/Http/Controllers/Auth/VerificationController.php',
];

foreach ($syncFromSource as $sourceUrl) {
	$contents = file_get_contents($baseUrl . $sourceUrl);
	file_put_contents($destination . $sourceUrl, $contents);
	echo "File {$sourceUrl} copied from remote to local." . PHP_EOL;
}
