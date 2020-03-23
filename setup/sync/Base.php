<?php


class Base implements Repo
{
    protected static $url = 'https://raw.githubusercontent.com/laravel/laravel/master/';

    protected static $files = [
        'README.md',
        'webpack.mix.js',
        'app/User.php',
        'app/Http/Kernel.php',
        'app/Http/Controllers/Controller.php'
    ];

    public static function getFiles(): array
    {
        return static::$files;
    }

    public static function getContent(string $file): string
    {
        return file_get_contents(static::$url . $file);
    }
}