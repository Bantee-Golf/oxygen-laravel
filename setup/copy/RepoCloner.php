<?php

class RepoCloner
{
    protected static $destinationDir = __DIR__ . '/../../LaravelDefaultFiles/';

    public static function clone(string $repoName)
    {
        $repo = self::getRepo($repoName);
        foreach ($repo->getFiles() as $file) {
            $content = $repo->getContent($file);
            static::copyFile($file, $content);
        }
    }

    /**
     * @param $name
     * @return Repo
     * @throws Exception
     */
    private static function getRepo($name): Repo
    {

        $class = new $name();
        if (!$class instanceof Repo) {
            throw new \Exception("{$name} must implement repo.");
        }

        return $class;
    }


    private static function copyFile($file, $content)
    {
        $destination = static::destinationFor($file);

        if (static::missingDirectory($destination)) {
            static::createDirectory($destination);
        }

        file_put_contents($destination, $content);
        echo "File {$file} copied from remote to local." . PHP_EOL;
    }

    private static function destinationFor($file)
    {
        return static::$destinationDir . $file;
    }

    private static function missingDirectory($destination)
    {
        $dir = dirname($destination);
        return !is_dir($dir);
    }

    private static function createDirectory($destination)
    {
        $dir = dirname($destination);
        mkdir($dir, 0777, true);
    }

}