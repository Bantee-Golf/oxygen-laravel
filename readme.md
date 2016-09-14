# Oxygen - Admin Dashboard for Laravel 5.3+

![Admin Dashboard](https://bitbucket.org/repo/Gdn48E/images/96070630-App%20Admin.png)

### [For Laravel 5.2, use version `0.1.3`](https://bitbucket.org/elegantmedia/oxygen-laravel/src/4f121b0574945a6278979f696b59f0c20637735c/?at=0.1.4).

## Summary

This package has the built-in support for the following.

- Auto-generated admin dashboard based on either HTML5 or AngularJS.
- Auto-generated frontend template (based on Bootstrap).
- User registrations, logins, roles, permissions and object level permission control.
- User invitations, invitation control, and user management.
- Single-tenant and Multi-tenant configuration.
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




1) Create a new Laravel project and go to the directory
```
composer create-project --prefer-dist laravel/laravel [project-name]
cd [project-name]
```


2) Update `composer.json`. 

This package and some dependent packages are available in private repositories. Change the `repositories` section to add the new repository, or create a new section in the file.

```
    "repositories": [
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/oxygen-laravel.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/laravel-generators.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/laravel-helpers.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/quickdata-laravel.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/multitenant-laravel.git"
        }
    ],
```

3) Require the package into composer through the command line.
```
composer require emedia/oxygen:0.0.x
```

4) Open `config/app.php` and add the following,
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

5) **(Optional Step)** It's suggested at this point you'll change the `public` dir to `public_html` as it's the convention used in most cPanel based servers.

To do this, run the following command.
```
php artisan scaffold:move:public
```

6) Create your `.env` file and add the database and other details.

7) Commit your changes to Git, because next step will change some of the default files.

8) After everything is done, run the following in the command line.

```
php artisan oxygen:setup
```

When you run the setup, it will ask ask a series of questions. Unless you want to change the default behaviour, you can accept the default answer for all of them and proceed. Whenever it asks to overwrite a file, say 'yes'.

After the installation is complete, see if it gives you any errors or other information. You might have to manually fix them.

9) The setup is now complete. Run the following to complete the installation.

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

10) Open the home page in a browser. Your default user login password is listed in the `database/seeds/UsersTableSeeder.php` file.

### Tips

On `.env` file, by adding `DASHBOARD_AUTHENTICATION=false`, it will skip the login process. This is helpful for development.

## After Installation

- You can add new features to existing controllers as needed. 
- If you need to change the default behaviour, you can create new classes or extend from the classes in the `Oxygen` package.
- Roles and Permissions are implemented with [Bouncer. Read the docs.](https://github.com/JosephSilber/bouncer)
- If you're going to use auto-syncing relationships (with `HandlesEntityCRUD` trait), **do not** use `Route::resource` for that controller. It will expose a security vulnerability.

## Additional Details

If you want to publish the views after the installation, run
```
php artisan vendor:publish --provider="EMedia\Oxygen\OxygenServiceProvider" --tag=views --force
```


## Issues/Bugs?
- Submit a pull request (on a new branch) or [submit an issue](https://bitbucket.org/elegantmedia/oxygen-laravel/issues).
- **Do not** commit new changes directly to the `master` branch.