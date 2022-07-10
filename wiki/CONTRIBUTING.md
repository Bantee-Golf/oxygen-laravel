# Contributing

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - Check the code style with `composer check-style` and fix it with `composer fix-style`.

- **Create feature branches** - Don't commit to master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.


## Local Project Setup

- Step 1 - Clone the repo to your local machine and move to that folder.
- Step 2 - Create a new branch on the cloned project. For example `feature/202003-add-settings`
- Step 3 - The easiest way for development is to setup a second Laravel project which will act as the parent project. For this example, we'll create a project called `Workbench` and it will have the local development URL `http://workbench.test`

```
composer create-project --prefer-dist laravel/laravel Workbench
cd Workbench
```

If you'd like to fetch the project from another branch, use the syntax below, where `dev-master` is the branch name.
```
composer create-project --prefer-dist laravel/laravel Workbench dev-master
```

- Step 4 - Then open `composer.json` on the test project, which will be located at `Workbench/composer.json`, and add the following config. This will [create a symlink](https://getcomposer.org/doc/05-repositories.md#path) to your cloned project. The `url` will be the path created on Step 1.

```
"repositories": [
    {
        "type": "path",
        "url": "../your-local-oxygen-cloned-path"
    },
]
```

In addition, go to [./INSTALLATION.md] and follow Step #2.1.

- Step 5 - Now add this project, but use the branch created at Step 2.

``` shell
composer require emedia/oxygen:"dev-feature/202003-add-settings"
```

OR Alternatively, you can add it to `composer.json`, and run `composer update`.
``` json 
	"emedia/oxygen": "dev-feature/202003-add-settings"
```

- Step 7 - Create a database called `workbench`

- Step 6 - At this point commit your changes to the TEST PROJECT, so you can rollback to this point easily. Note that you're NOT COMMITING changes to oxygen project. The changes are commited to the TEST project, in this example, `Workbench` folder.

```shell
# Commit your changes to the TEST project
git init
git add . && git commit -m "Initial commit"
```

```shell
# Reset the TEST project to the previous commit and remove unstaged changes
# Do this if you want to rollback to the last commit and remove unstaged changes
git reset --hard HEAD~1 && git clean -fd
```

## Test Installation and Rollback

Now you can run the test installer.

```
php artisan oxygen:dashboard:install --name Workbench --dev_url workbench.test --email apps@elegantmedia.com.au --dbname workbench --dbpass root --mailhost "0.0.0.0" --mailport 1025
```

Migrate and seed

```
php artisan db:refresh
```

Run the project

```
npm run watch
```

Now you can update files on either projects. If there are any errors, rollback to last commit.


## Development Tools

Periodically, you'll have to sync files from Laravel's main project back so the local files are updated. Use the following command to do that.

```
php ./setup/SyncFromSource.php
```

Run the tests

```
vendor/bin/phpunit
```


## Production Tools

Always ensure the tests pass before you send a pull-request.

Before releasing a version, the assets must be pre-compiled on the `publish` directory.

```
cd publish
npm run build
# This will create a `node_modules` folder in the `publish` directory. But we don't want that.
# So remove it.
rm -rf node_modules
```


# Test Deployment Pipeline on a Local Machine

You need these before starting.

1. Docker installed and running
2. A valid SSH key. Default is `~/.ssh/id_rsa`. This can be changed in `docker-compose.yml` and `setup/pipeline.sh`

Step 1: Setup all containers
```
docker-compose up
```

Step 2: SSH to PHP container (on a new tab/window)
```
docker exec -it appcontainer /bin/bash
```

Step 3: Run the shell script
The shell script will have similar commands to the Pipeline. Because now you're in the container, you'll have more control to see what happens, and can update the commands.
```
sh setup/pipeline.sh
```

Step 4: (Optional) See the application

Dusk Screenshots - you can copy the screenshots generated to a shared volume
```
// Run this from Docker container
rm /test_screenshots/*.png && cp /laravel_app/tests/Browser/screenshots/*.png /test_screenshots/
```

View the application
```
http://localhost:8095/
```

View the mail log
```
http://localhost:8025/
```

Connect to the Database
```
Host: 127.0.0.1
Port: 3305
User: root
Pass: root
```

Selenium Container
```
http://localhost:4444/
```

// Step 4: Update the files
If you make a change, ensure that you update BOTH `setup\pipeline.sh` and `bitbucket-pipelines.yml` with changes.


# Common Issues & Troubleshooting

### Test Errors

1. **Test Error: Did not see expected text [Laravel] within element [body].**

    - If you see this, or errors, check if you're still using a cached environment. Use `php artisan config:cache` to clear cache and try again.

### Build Pipeline Errors

1. If a build pipeline fails, first check the actual root cause of the issue. You'll have to see the errors on the pipeline and then try to re-produce the error locally.
2. Common cause of errors are 

   - Out of sync files with Laravel source. See 'Sync with Source' instructions above.
   - A dependent library was updated. Check the first error date for this.
