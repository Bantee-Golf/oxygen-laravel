<?php


namespace Tests\setup;


use PHPUnit\Framework\TestCase;

/**
 * Class SyncFromSourceTest
 *
 * Default Laravel files are required by Oxygen to modify,
 * or reference. These should already exist in the repo.
 * If they don't, SyncFromSource will copy them over.
 *
 * @package Tests\setup
 */
class SyncFromSourceTest extends TestCase
{
    /**
     * @test
     */
    public function test_SyncFromSource_execute_should_create_default_laravel_files()
    {
        $directory =  dirname(__FILE__) . '/../../LaravelDefaultFiles';

        // Delete Auth dir to make sure it's re-downloaded
        $authDirectory = $directory . '/app/Http/Controllers/Auth';
        if (is_dir($authDirectory)) {
            static::deleteDirectory($authDirectory);
        }
        $this->assertFalse(is_dir($authDirectory));

        // Run SyncFromSource
        include(dirname(__FILE__) . '/../../setup/SyncFromSource.php');

        $this->assertTrue(is_dir($directory));
        //  Nested file downloaded, and required
        // directories in path created.
        $this->assertTrue(file_exists($authDirectory . '/ForgotPasswordController.php'));
    }

    /**
     * Small helper to recursively delete directory
     *
     * @param $dir
     */
    private static function deleteDirectory($dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) static::deleteDirectory("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }
}