<?php


class Base implements Repo
{

    private static $url = 'https://raw.githubusercontent.com/laravel/laravel/master/';
    private $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public  function getFiles(): array
    {
        return $this->files;
    }

    public function getContent(string $file): string
    {
        return file_get_contents(static::$url . $file);
    }
}