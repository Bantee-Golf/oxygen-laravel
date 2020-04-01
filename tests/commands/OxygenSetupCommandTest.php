<?php


namespace Tests\commands;


use EMedia\AppSettings\AppSettingsServiceProvider;
use EMedia\Devices\DeviceAuthServiceProvider;
use EMedia\Generators\GeneratorServiceProvider;
use EMedia\LaravelTestbench\FilesTestCase;
use EMedia\LaravelTestbench\Repo\Laravel\Base;
use EMedia\LaravelTestbench\Repo\RepoCopy;
use EMedia\Oxygen\OxygenServiceProvider;
use EMedia\PHPHelpers\Files\FileManager;
use Laravel\Ui\UiServiceProvider;

class OxygenSetupCommandTest extends FilesTestCase
{
    protected $appName = "My Test App";
    protected $email = "testing_super_admin@elegantmedia.com.au";
    protected $devUrl = 'oxygen.test';

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->copyLaravelRepoFiles();
    }

    protected function copyLaravelRepoFiles()
    {

        $base = new Base([
            "routes/web.php",
            "routes/api.php",
            'app/Http/Kernel.php',
            'app/Providers/RouteServiceProvider.php',
            ".env.example"
        ]);

        $copy = new RepoCopy($this->laravelPath);
        $copy->get($base);
    }

    protected function getPackageProviders($app)
    {
        return [
            OxygenServiceProvider::class,
            GeneratorServiceProvider::class,
            AppSettingsServiceProvider::class,
            DeviceAuthServiceProvider::class,
            UiServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('oxygen.abilityModel', '');
        $app['config']->set('oxygen.roleModel', '');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_sets_up_a_project()
    {
        $this->artisan('setup:oxygen-project')
             ->expectsQuestion('What is the project name?', $this->appName)
             ->expectsQuestion('What is the `from` email address for system emails? (Press ENTER key for default)',
                 $this->email)
             ->expectsQuestion('What is your email to seed the database? (Press ENTER key for default)', $this->email)
             ->expectsQuestion('What is the local development URL? (Press ENTER key for default)', $this->devUrl)
             ->assertExitCode(0);

        $this->assertPublicHtmlCreated()
             ->assertVariablesReplaced()
             ->assertMigrationsGenerated()
             ->assertRoutesModified()
             ->assertMiddlewaresUpdated()
             ->assertClassesGenerated()
             ->assertFilesPublished()
             ->assertSetupSettings()
             ->assertSetupDevices()
             ->assertEnvSet()
             ->assertReadMeUpdated();

        $this->artisan('setup:oxygen-project')
             ->expectsQuestion('What is the project name?', $this->appName)
             ->expectsQuestion('What is the `from` email address for system emails? (Press ENTER key for default)',
                 $this->email)
             ->expectsQuestion('What is your email to seed the database? (Press ENTER key for default)', $this->email)
             ->expectsQuestion('What is the local development URL? (Press ENTER key for default)', $this->devUrl)
             ->expectsQuestion("{$this->laravelPath}/webpack.mix.js already exists. Overwrite?", true)
             ->expectsQuestion("{$this->laravelPath}/readme.md already exists. Overwrite?", true)
             ->expectsQuestion("{$this->laravelPath}/resources/lang/en/auth.php already exists. Overwrite?", true)
             ->expectsQuestion("{$this->laravelPath}/apidoc.json already exists. Overwrite?", true)
             ->expectsQuestion("{$this->laravelPath}/app/User.php already exists. Overwrite?", true)
             ->expectsQuestion("Oxygen routes are already in routes file. Add again?", true)
             ->expectsQuestion("Oxygen API routes are already in routes file. Add again?", true)
             ->expectsQuestion("Update Http/Kernel.php with new middleware?", true)
             ->expectsQuestion("App Settings package routes are already in routes file. Add again?", true)
             ->expectsQuestion("Devices package routes are already in routes file. Add again?", true)
             ->expectsQuestion("Oxygen ENV values are already in {$this->laravelPath}/.env. Add again?", true)
             ->expectsQuestion("Oxygen ENV values are already in {$this->laravelPath}/.env.example. Add again?", true)
             ->assertExitCode(0);
    }


    protected function assertPublicHtmlCreated()
    {
        $publicHtml = "{$this->laravelPath}/public_html";
        $this->assertFileExists($publicHtml);
        return $this;
    }

    /**
     * @throws \EMedia\PHPHelpers\Exceptions\FileSystem\FileNotFoundException
     * @throws \Exception
     */
    protected function assertVariablesReplaced()
    {
        $variables = [
            ['app/Providers/RouteServiceProvider.php', "public const HOME = '/dashboard'"],
            [".env.example", "APP_NAME=" . '"' . $this->appName . '"'],
            [".env.example", "APP_URL=http://{$this->devUrl}"],
            ["database/seeds/Auth/UsersTableSeeder.php", $this->email],
            ["webpack.mix.js", $this->devUrl],
            ["config/oxygen.php", "'multiTenantActive' => false"]
        ];

        foreach ($variables as $variable) {
            $file = $variable[0];
            $text = $variable[1];
            $path = "{$this->laravelPath}/$file";
            $replaced = FileManager::isTextInFile($path, $text);

            if (!$replaced) {
                throw new \Exception("Missing text '{$text}' in file '{$file}''");
            }
        }

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function assertMigrationsGenerated()
    {
        $files = [
            "create_role_permission_tables.php",
            "alter_users_table.php",
            "create_invitations_table.php",
            "create_files_table.php"
        ];

        foreach ($files as $file) {
            $path = $this->laravelPath . "/database/migrations/*" . $file;
            $results = glob($path);

            if (count($results) === 0) {
                throw new \Exception("Missing migration '${file}''");
            }

        }

        return $this;
    }

    /**
     * @throws \EMedia\PHPHelpers\Exceptions\FileSystem\FileNotFoundException
     * @throws \Exception
     */
    protected function assertRoutesModified()
    {
        $routes = [
            "web" => ["oxygen::pages.welcome"],
            "api" => ["Route::get ('/profile', 'Auth\ProfileController@index')"]
        ];

        foreach ($routes as $route => $texts) {
            foreach ($texts as $text) {
                $path = "{$this->laravelPath}/routes/{$route}.php";
                $replaced = FileManager::isTextInFile($path, $text);

                if (!$replaced) {
                    throw new \Exception("Missing '{$text}' in route '{$route}''");
                }
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \EMedia\PHPHelpers\Exceptions\FileSystem\FileNotFoundException
     */
    protected function assertMiddlewaresUpdated()
    {
        $kernelFile = "{$this->laravelPath}/app/Http/Kernel.php";

        $middlewares = [
            "'auth.acl' => \EMedia\Oxygen\Http\Middleware\AuthorizeAcl::class",
            "'auth.api' => \EMedia\Oxygen\Http\Middleware\ApiAuthenticate::class",
            "'auth.acl' => \EMedia\Oxygen\Http\Middleware\AuthorizeAcl::class",
            "\EMedia\Oxygen\Http\Middleware\LoadViewSettings::class",
            '\EMedia\Oxygen\Http\Middleware\ParseNonPostFormData::class'
        ];

        foreach ($middlewares as $middleware) {
            $inserted = FileManager::isTextInFile($kernelFile, $middleware);
            if (!$inserted) {
                throw new \Exception("Middleware '{$middleware}' missing from app/Http/Kernel.php");
            }
        }

        return $this;
    }

    protected function assertClassesGenerated()
    {
        $class = "DashboardController";

        $controller = "{$this->laravelPath}/app/Http/Controllers/{$class}.php";
        $generated = file_exists($controller);
        if (!$generated) {
            throw new \Exception("Class {$class} missing; Were classes properly generated?");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function assertFilesPublished()
    {
        $oxygenViews = "{$this->laravelPath}/resources/views/vendor/oxygen";
        $generated = is_dir($oxygenViews);
        if (!$generated) {
            throw new \Exception("Oxygen views missing; Were files properly published?");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function assertSetupSettings()
    {
        $setingsMigration = "{$this->laravelPath}/database/migrations/*_create_settings_table.php";
        $results = glob($setingsMigration);

        if (count($results) === 0) {
            throw new \Exception("Missing settings migration file; was 'setup:package:app-settings' called?");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function assertSetupDevices()
    {
        $setingsMigration = "{$this->laravelPath}/database/migrations/*_create_devices_table.php";
        $results = glob($setingsMigration);

        if (count($results) === 0) {
            throw new \Exception("Missing devices migration file; was 'setup:package:devices' called?");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \EMedia\PHPHelpers\Exceptions\FileSystem\FileNotFoundException
     */
    protected function assertEnvSet()
    {
        $env = "{$this->laravelPath}/.env";
        $appendedHeader = "### Oxygen Settings Start ###";
        $appended = FileManager::isTextInFile($env, $appendedHeader);
        if (!$appended) {
            throw new \Exception("Missing {$appendedHeader} in .env; were .env variables properly appended?");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \EMedia\PHPHelpers\Exceptions\FileSystem\FileNotFoundException
     */
    protected function assertReadMeUpdated()
    {
        $env = "{$this->laravelPath}/readme.md";
        $appendedHeader = "## Local Development Setup Instructions";
        $appended = FileManager::isTextInFile($env, $appendedHeader);
        if (!$appended) {
            throw new \Exception("Missing {$appendedHeader} in readme.me; was the readme udpated correctly?");
        }

        return $this;
    }
}