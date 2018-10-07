# Oxygen - Admin Dashboard for Laravel 5.7+

![Admin Dashboard](https://bitbucket.org/repo/Gdn48E/images/96070630-App%20Admin.png)

### [For Laravel 5.2, use version `0.1.3`](https://bitbucket.org/elegantmedia/oxygen-laravel/src/4f121b0574945a6278979f696b59f0c20637735c/?at=0.1.4)
### [For Laravel 5.4, use version `1.0.8`](https://bitbucket.org/elegantmedia/oxygen-laravel/src/9124e7b33c645709867634134121fd9c407ffb73/?at=1.0.8)
### For Laravel 5.6, use version `1.1`

## Summary

This package has the built-in support for the following.

- Auto-generated admin dashboard.
- Auto-generated frontend template (based on Bootstrap).
- User registrations, logins, roles, permissions and object level permission control.
- User invitations, invitation control, and user management.
- apidoc.js Template
- Auto-sync nested relationships when creating/updating objects (Use this with care!!!).
- Scaffolding to generate Entities, Repositories and Controllers.
- Seeding for Users, Roles.
- Middleware for API Key validation, Role Based Access Control, Share view settings.
- Extensible Authentication controllers to add your own features.

## Requirements

The following are required for a successful installation.

- [Laravel 5.7+](https://laravel.com/docs/5.7)

To install CSS JS for the dashboard, the following are required.

- [NodeJS with NPM or yarn](https://docs.npmjs.com/getting-started/installing-node)
- [Bower](http://bower.io/#install-bower)

## Installation

This package is intended to be installed on a **new Laravel project**. You'll be able to install it on an existing project, but might need to change some configuration settings.


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
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/oxygen-laravel.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/multitenant-laravel.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/lotus.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/formation.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/laravel-app-settings.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/laravel-generators.git"
        },
        {
            "type":"vcs",
            "url":"git@bitbucket.org:elegantmedia/quickdata-laravel.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/laravel-helpers.git"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:elegantmedia/php-helpers.git"
        }
    ],
```

3) Require the package into composer through the command line.
```
composer require silber/bouncer v1.0.0-rc.3
composer require emedia/oxygen
```

4) Edit `.env` file and update the database settings.

5) Commit your current state to Git, because next step will change some of the default files.

```
git init
git add -A && git commit -m "Initial commit."
```

6) Run the following command. This will do the default installation, if any questions are asked, you can just press ENTER to confirm the default choice, or change it.

```
php artisan setup:oxygen-project
```

(OPTIONAL) The default setup will install with default options. If you want to have manual control over installation run the command `php artisan setup:oxygen-project --confirm` and it will confirm before every step.

7) After the setup is done, you'll see the next steps on screen. These build instructions will be also added to your `README.md` file.

8) Go and make something amazing!

## After Installation

- You can add new features to existing controllers as needed. 
- If you need to change the default behaviour, you can create new classes or extend from the classes in the `Oxygen` package.
- Roles and Permissions are implemented with [Bouncer. Read the docs.](https://github.com/JosephSilber/bouncer)

### Customisations

#### How to Overwrite Views After Installation
If you want to publish the views after the installation, run
```
php artisan vendor:publish --provider="EMedia\Oxygen\OxygenServiceProvider" --tag=views --force
```

## FAQs

#### I got an error while installing, what do to?

Probably it's a conflict with an previously partially completed setup. If this happens, rollback everything to the commit at Step #5, and try the steps from there again.

```
// use this command to hard reset all files and remove any new files
git reset --hard && git clean -fd
```

#### How to Change the User model

1 . On `config/auth.php`, change `providers.users.model` OR add a new line,
```
	'model'	=> '\Auth\NewUserClass',
```

2 . On `AppServiceProvider.php` and `boot` method, change as below,
```
public function boot()
{
    \Silber\Bouncer\Database\Models::setUsersModel(NewUserClass::class);
}
``` 

#### What are the logins?

Your default user login password is listed in the `database/seeds/Auth/UsersTableSeeder.php` file.

## Found an Issue or a Bug?

- Submit a pull request (on a new branch) or [submit an issue](https://bitbucket.org/elegantmedia/oxygen-laravel/issues).
- **DO NOT** commit new changes directly to the `master` branch. Create a development branch, and then send a pull-request to master, and get someone else to review the code before merging.