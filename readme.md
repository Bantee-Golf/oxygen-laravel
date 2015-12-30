# Oxygen - Admin Dashboard for Laravel 5.2+


## Summary

This package has the built-in support for the following.

- Auto-generated admin dashboard based on either HTML5 or AngularJS.
- Auto-generated frontend template (based on Bootstrap).
- User registrations, logins, roles, permissions and object level permission control.
- User invitations, invitation control, and user management.
- Single-tenant, and Multi-tenant configuration.
- ApiDoc.js Template
- Auto-sync nested relationships when creating/updating objects (Use this with care!!!).
- Auto-handles CRUD requests in Controllers based on extended repositories.
- Scaffolding to generate Entities, Repositories and Controllers.
- Seeding for Tenants (for Multi-Tenants) and Users, Roles.
- Middleware for API Key validation, Role Based Access Control, Share view settings.
- Extensible Authentication controllers to add your own features.

## Requirements

The following are required for a successful installation.
- [Laravel 5.2+](https://laravel.com/docs/5.2#installing-laravel)

To install CSS JS for the dashboard, the following are required.
- [NodeJS with NPM](https://docs.npmjs.com/getting-started/installing-node)
- [Bower](http://bower.io/#install-bower)

## Installation

This package is intended to be installed on a **new Laravel project**. You'll be able to install it on an existing project, but might need to change some configuration.


1) Create a new Laravel project
```
laravel new star-wars
```

2) It's suggested at this point you'll change the `public` dir to `public_html` as it's the convention used in most cPanel based servers.

- Rename `public` folder to `public_html`
- In `app\Providers\AppServiceProvider.php` change the `boot()` function contents.
```
	public function boot()
	{
	    $this->app->bind('path.public', function() {
	        return base_path().'/public_html';
	    });
	}
```
- Change it in `server.php` if you intend to use the built-in web server.

3) Update `composer.json`. 

This package and some dependent packages are available in private repositories. Change the `repositories` section to add the new repository, or create a new section in the file.

```
	"repositories": [
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/oxygen-laravel.git"
        }
    ],
```

4) Require the package into composer through the command line.
```
composer require emedia\oxygen
```

5) Open `config/app.php` and add the following,
At the end of `providers` add:
```
	EMedia\Oxygen\OxygenServiceProvider::class,
    EMedia\MultiTenant\MultiTenantServiceProvider::class,
    EMedia\Generators\GeneratorServiceProvider::class,
    Silber\Bouncer\BouncerServiceProvider::class,
```

At the end of `aliases` add:
```
	'TenantManager' => EMedia\MultiTenant\Facades\TenantManager::class,
	'Bouncer' => Silber\Bouncer\BouncerFacade::class,
```

6) Create your `.env` file and add the database and other details.

7) After everything is done, run the following in the command line.

Before running the commands below, commit your changes to Git, because it will alter the default settings in your Laravel project.

```
php artisan oxygen:setup
```

When you run the setup, it will ask ask a series of questions. Unless you want to change the default behaviour, you can accept the default answer for all of them and proceed. Whenever it asks to overwrite a file, say 'yes'.

After the installation is complete, see if it gives you any errors or other information. You might have to manually fix them.

8) The setup is now complete. Run the following to complete the installation.

Install required CSS, JS for the project.
```
bower install
```

Migrate and seed the database
```
composer dump-autoload
php artisan migrate
php artisan db:seed
```

9) Open the home page in a browser. Your default user login password is listed in the `database/seeds/UsersTableSeeder.php` file.

### Tips

On `.env` file, by adding `DASHBOARD_AUTHENTICATION=false`, it will skip the login process. This is helpful for development.

## After Installation

- You can add new features to existing controllers as needed. 
- If you need to change the default behaviour, you can create new classes or extend from the classes in the `Oxygen` package.
- Roles and Permissions are implemented with [Bouncer. Read the docs.](https://github.com/JosephSilber/bouncer)
- If you're going to use auto-syncing relationships (with `HandlesEntityCRUD` trait), **do not** use `Route::resource` for that controller. It will expose a security vulnerability.


## Issues/Bugs?
- Submit a pull request (on a new branch) or log an issue.
- **Do not** commit new changes directly to the `master` branch.