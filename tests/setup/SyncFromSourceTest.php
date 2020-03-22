<?php


namespace Tests\setup;


use PHPUnit\Framework\TestCase;

class SyncFromSourceTest extends TestCase
{
    private static $directory = '../../LaravelDefaultFiles';

    /**
     * @test
     */
    public function test_SyncFromSource_execute_should_create_default_laravel_files()
    {
        if (is_dir(static::$directory)) {
            static::deleteDirectory(static::$directory);
        }

        $this->assertFalse(is_dir(static::$directory));

        include('../../setup/SyncFromSource.php');

        $this->assertTrue(is_dir(static::$directory));

        $this->assertTrue(file_exists(static::$directory . '/app/Http/Controllers/Auth/ForgotPasswordController.php'));
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