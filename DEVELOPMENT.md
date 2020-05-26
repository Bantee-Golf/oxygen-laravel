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

### How to Run Pipelines Locally for Testing

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
```

### Common Issues

**Test Error: Did not see expected text [Laravel] within element [body].**

If you see this, or errors, check if you're still using a cached environment. Use `php artisan config:cache` to clear cache and try again.

