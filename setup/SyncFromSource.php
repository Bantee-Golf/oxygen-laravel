<?php
require('clone/Repo.php');
require('clone/RepoCloner.php');
require('clone/Base.php');
require('clone/UI.php');

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

$cloner = new RepoCloner(__DIR__ . '/../LaravelDefaultFiles/');

foreach ($repos as $repo) {
    $cloner->clone($repo);
}