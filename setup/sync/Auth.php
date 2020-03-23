<?php


class Auth implements Repo
{
    protected static $baseUrl = 'https://raw.githubusercontent.com/laravel/ui/2.x/';

    protected static $files = [
        // 'app/Http/Controllers/Auth/ConfirmPasswordController.php',
        'app/Http/Controllers/Auth/ForgotPasswordController.php',
        'app/Http/Controllers/Auth/LoginController.php',
        'app/Http/Controllers/Auth/RegisterController.php',
        'app/Http/Controllers/Auth/ResetPasswordController.php',
        'app/Http/Controllers/Auth/VerificationController.php',
    ];

    public static function getFiles(): array
    {
        return static::$files;
    }

    public static function getContent(string $file): string
    {
        $url = static::$baseUrl . static::stubPathFor($file);
        return file_get_contents($url);
    }

    private static function stubPathFor(string $file)
    {
        $path =  substr($file, strpos($file, 'Auth'));
        $stub = str_replace(".php", ".stub", $path);
        return 'stubs/'. $stub;
    }
}