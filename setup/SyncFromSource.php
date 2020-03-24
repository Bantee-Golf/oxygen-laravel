<?php

use Setup\Copy\Base;
use Setup\Copy\RepoCopy;
use Setup\Copy\UI;

$vendorPath = dirname(__FILE__) . '/../vendor/autoload.php';
require($vendorPath);

$repos = [
    new Base([
        'README.md',
        'webpack.mix.js',
        'app/User.php',
        'app/Http/Kernel.php',
        'app/Http/Controllers/Controller.php'
    ]),
    new UI([
        // 'app/Http/Controllers/Auth/ConfirmPasswordController.php',
        'app/Http/Controllers/Auth/ForgotPasswordController.php',
        'app/Http/Controllers/Auth/LoginController.php',
        'app/Http/Controllers/Auth/RegisterController.php',
        'app/Http/Controllers/Auth/ResetPasswordController.php',
        'app/Http/Controllers/Auth/VerificationController.php',
    ])
];

$cloner = new RepoCopy(__DIR__ . '/../LaravelDefaultFiles/');

foreach ($repos as $repo) {
    $cloner->copy($repo);
}