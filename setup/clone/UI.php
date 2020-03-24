<?php


class UI implements Repo
{

    protected static $url = 'https://raw.githubusercontent.com/laravel/ui/2.x/';
    private $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getContent(string $file): string
    {
        $url = static::$url . $this->stubPathFor($file);
        return file_get_contents($url);
    }

    private function stubPathFor(string $file)
    {
        $path =  substr($file, strpos($file, 'Auth'));
        $stub = str_replace(".php", ".stub", $path);
        return 'stubs/'. $stub;
    }
}