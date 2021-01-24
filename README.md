# Oxygen - Admin Dashboard for Laravel

![Admin Dashboard](https://bitbucket.org/repo/Gdn48E/images/570218285-Workbench%20Dashboard%202020-11-04%2015-52-49.png)

## Version Compatibility

| Laravel Version   | Oxygen Version    | Branch           |
| -----------------:| ----------------- |------------------|
| v8                | 5.x               | master           |
| v7                | 4.x               | version/v4.x     |

See [change log for change history](CHANGELOG.md) and compatibility with past versions.

## System Requirements

- PHP 7.3+
- [NodeJS with NPM](https://docs.npmjs.com/getting-started/installing-node)


## Installation

The easiest way to do it is by using the [Oxygen Installer](https://bitbucket.org/elegantmedia/oxygen-installer). With the installer, you can create a project with one line.

```
oxygen new myproject --name 'Oxygen' --email apps@elegantmedia.com.au --devurl 'localhost.test'
```

If you don't want to use the installer, you can install it with manual setup. [See Manual Setup Instructions](INSTALLATION.md). After the setup is done, you'll see the next steps on screen. These build instructions will be also added to your `README.md` file.

## Developer Commands

These commands are available from the CLI.

#### Create a New User

Create a new user and assign a role.
```
php artisan setup:create-user
```

#### Scaffold Views

Create the default views for a given resource.

```
php artisan scaffold:views
OR
php artisan scaffold:views --path=<resource.path>

Example:
php artisan scaffold:views --path=manage.users
```

This will create the default views within `resources/views/manage/users`, or in the path that you specify.


## Helper Functions

```
// 08/Oct/2018 10:27 PM
{{ standard_datetime($item->created_at) }}

// 08/Oct/2018
{{ standard_date($item->created_at) }}

// 10:27 PM
{{ standard_time($item->created_at) }}
```

## Middleware

### Access Control Lists (ACL) Middleware

Oxygen comes with an access control middleware based on roles and/or permissions. You can use the middleware for advanced permission based access control for routes, controllers or functions.

To use the middleware, use `auth.acl`.

```
Usage:

// User must have at least `owner` OR `admin` AND the permission `do-something`
auth.acl:roles[owner|admin],permissions[do-something]

// User must have at least `owner` OR `admin`
auth.acl:roles[owner|admin]

// User must have the permission `do-something`
auth.acl:permissions[do-something]

// User must have both permissions. `do-something` AND `do-another-thing`
auth.acl:permissions[do-something|do-another-thing]

// User must have at least one permission `do-something` OR `do-something-else`
auth.acl:permissions[do-something OR do-something-else]
```

### API Key Check Middleware

Call the `auth.api` middleware to verify API keys before hitting specific routes. The middleware will look for `X-Api-Key` field in HTTP header.

You should provide the valid API Keys in the `.env` file.

```
Example in .env file:
API_KEY="123123123"
OR
API_KEY="123123123,APIKEY2"
```


### Reserved Variables in Blade Templates

These are the reserved variables in Blade templates. These variables should not be used to assign other data.

Blade Variables
```
{{-- Current logged in User is available with $user by default --}}
{{ $user->name }}

{{-- Current page title. `My Account` by default --}}
{{ $pageTitle }}

{{-- Current app name --}}
{{ $appName }}
```

## Must Read Instructions

Oxygen by default has a lot of built-in functions. Please read all the docs to understand all features. Otherwise you'll be spending a lot of time re-doing existing features.

| Library | What it Does |
| ------- | ------------ |
| [Bouncer](https://github.com/JosephSilber/bouncer)                                                    | Access, Roles and Permission Handling |
| [Formation](https://bitbucket.org/elegantmedia/formation/src/master/README.md)                        | Form Builder |
| [Fortify](https://github.com/laravel/fortify)                                                         | Laravel Authentication |
| [Oxygen App Settings](https://bitbucket.org/elegantmedia/laravel-app-settings/src/master/README.md)   | App setting storage and retrieval |
| [Oxygen Devices]((https://bitbucket.org/elegantmedia/devices-laravel/src/master/README.md))           | Device Authenticator for API Requests |
| [Laravel API Helpers](https://bitbucket.org/elegantmedia/laravel-api-helpers/src/master/README.md)    | API and Documentation Generator |
| [Laravel Media Manager](https://bitbucket.org/elegantmedia/laravel-api-helpers/src/master/README.md)  | File and Media Handling Library |
| [Laravel Test Kit](https://bitbucket.org/elegantmedia/laravel-test-kit/src/master/README.md)          | Integration Testing Helper Library |
| [Lotus]((https://bitbucket.org/elegantmedia/lotus/src/master/README.md))                              | Breadcrumbs, Page Titles, Tables, Pagination, Empty State and other Html Elements |
| [PHP Toolkit](https://github.com/elegantmedia/PHP-Toolkit)                                            | PHP Utility Library |

## After Installation

- You can add new features to existing controllers as needed.
- If you need to change the default behaviour, you can create new classes or extend existing classes.

### Customisations

#### How to Overwrite Views After Installation
If you want to publish the views after the installation, run
```
php artisan vendor:publish --provider="EMedia\Oxygen\OxygenServiceProvider" --tag=views --force
```

## Common Issues

#### I got an error while installing, what do to?

Probably it's a conflict with an previously partially completed setup. If this happens, rollback everything to the commit at Step #5, and try the steps from there again.

```
// use this command to hard reset all files and remove any new files - NEVER DO THIS ON A LIVE SERVER!
git reset --hard && git clean -fd
```

#### How to Change the User model

1 . On `config/auth.php`, change `providers.users.model` OR add a new line,
```
	'model'	=> '\Auth\NewUserClass',
```

2 . On `OxygenServiceProvider.php` update the `boot()` method.
```
public function boot()
{
    \Silber\Bouncer\BouncerFacade::useUserModel(\Auth\NewUserClass::class);
}
```

#### What are the logins?

Your default user login password is listed in the `database/seeds/Auth/UsersTableSeeder.php` file.

## Found an Issue or a Bug?

Don't stay quiet and ignore any issues or improvement suggestions.

- [Create an Issue](https://bitbucket.org/elegantmedia/oxygen-laravel/issues?status=new&status=open)
- Submit a pull request (on a new branch) or [submit an issue](https://bitbucket.org/elegantmedia/oxygen-laravel/issues).
- **DO NOT** commit new changes directly to the `master` branch. Create a development branch, and then send a pull-request to master, and get someone else to review the code before merging.
- Please see [contributing guidelines](CONTRIBUTING.md) and for details.

## Development Notes

See [CONTRIBUTING.md](CONTRIBUTING.md) for more developer and local setup instructions.

## Copyright

Copyright (c) 2020 Elegant Media.
