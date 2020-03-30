<?php


namespace Tests\setup;


use PHPUnit\Framework\TestCase;

class SyncFromSourceTest extends TestCase
{


    /**
     * @test
     */
    public function test_SyncFromSource_execute_should_create_default_laravel_files()
    {

        $directory =  dirname(__FILE__) . '/../../LaravelDefaultFiles';

        $authDirectory = $directory . '/app/Http/Controllers/Auth';
        if (is_dir($authDirectory)) {
            static::deleteDirectory($authDirectory);
        }
        $this->assertFalse(is_dir($authDirectory));

        include(dirname(__FILE__) . '/../../setup/SyncFromSource.php');

        $this->assertTrue(is_dir($directory));
        $this->assertTrue(file_exists($authDirectory . '/ForgotPasswordController.php'));
    }

    private static function deleteDirectory($dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) static::deleteDirectory("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }
}