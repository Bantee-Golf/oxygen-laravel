<?php


namespace Setup\Copy;



class RepoCopy
{

    /**
     * @var string
     */
    private $destinationDir;

    public function __construct(string $destinationDir)
    {
        $this->destinationDir = $destinationDir;
    }


    public function copy(Repo $repo)
    {
        foreach ($repo->getFiles() as $file) {
            $content = $repo->getContent($file);
            $this->copyFile($file, $content);
        }
    }

    private function copyFile($file, $content)
    {
        $destination = $this->destinationFor($file);

        if ($this->missingDirectory($destination)) {
            $this->createDirectory($destination);
        }

        file_put_contents($destination, $content);
        echo "File {$file} copied from remote to local." . PHP_EOL;
    }

    private function destinationFor($file)
    {
        return $this->destinationDir . $file;
    }

    private function missingDirectory($destination)
    {
        $dir = dirname($destination);
        return !is_dir($dir);
    }

    private function createDirectory($destination)
    {
        $dir = dirname($destination);
        mkdir($dir, 0777, true);
    }
}