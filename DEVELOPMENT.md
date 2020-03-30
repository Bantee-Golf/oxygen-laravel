## Development Notes


#### Sync Files from Laravel's Main Repo
During development, copy the latest required files from the source repo to local.

```
php ./setup/SyncFromSource.php
```

#### Local Development Setup

When developing a package locally, you can refer to a folder under development from another project. Do this during testing, so that you don't make unnecessary commits (for things that you're not sure about)

Example:

`composer.json` on the primary project.
```
    {
        "type": "path",
        "url": "../OxygenLaravel"
    },
```

The above will make a symlink to the composer package on your local machine.

And to refer to branch use,
```
composer require emedia/oxygen:"dev-laravel70"
OR
"emedia/oxygen": "dev-laravel70"
```

In the example above, you'll be working on a branch named `laravel70`. [See here for more info](https://getcomposer.org/doc/05-repositories.md#path).