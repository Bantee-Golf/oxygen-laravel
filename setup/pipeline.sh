#!/bin/bash
set -e

# LOCAL-STEP: Allow SSH access - this should be done early
# procps for `ps`
apt-get install -y vim procps
eval "$(ssh-agent)"
# ssh-add -l # use this to view existing identities
ssh-add ~/.ssh/id_rsa
ssh-keyscan bitbucket.org >> ~/.ssh/known_hosts
# use `ssh -T hg@bitbucket.org` to test the connection
# Docs: https://confluence.atlassian.com/bitbucket/troubleshoot-ssh-issues-271943403.html

# set common aliases
alias a='php artisan'
alias ll='ls -la'
BITBUCKET_CLONE_DIR=$(pwd)

# step: Install a new Laravel project
cd ..
rm -rf ./laravel_app
composer create-project --prefer-dist laravel/laravel="7.*" laravel_app --no-progress
cd laravel_app

# step: Update composer values
php $BITBUCKET_CLONE_DIR/setup/oxygen-install.php --run add-repositories
php $BITBUCKET_CLONE_DIR/setup/oxygen-install.php --run set-local-repo --path $BITBUCKET_CLONE_DIR
composer require emedia/oxygen:"@dev" --no-progress --no-interaction --ignore-platform-reqs --no-suggest

# if the symlink doesn't work, it can be because of an existing local copy
# try removing composer.lock and vendor directory and installing again.

# step: Install Oxygen
php artisan setup:oxygen-project --name Oxygen --email apps@elegantmedia.com.au --devurl localhost.devv:8000 --dbhost dbcontainer --dbuser appuser --dbpass userpass --mailhost mailhog --mailport 1025 --no-interaction
composer dump-autoload
npm install && npm run dev && npm run dev

# step: test the Oxygen package setup
./vendor/bin/phpunit

# step: install dusk
composer require laravel/dusk --dev
php artisan dusk:install

# step: change chrome port to selenium container
# this should run after `dusk:install`
sed -i 's#localhost:9515#selenium:4444/wd/hub#g' /laravel_app/tests/DuskTestCase.php

# step: set .env for dusk
cp .env .env.dusk.local

# step: clear config cache - always do this after changing .env file
php artisan config:cache

# step: migrate and seed DB
php artisan db:refresh

# step: Start the dev server in background
php artisan serve --host=0.0.0.0 --port=8000 > /dev/null 2>&1 &

# If you want to stop the server, find the process IDs and kill them
# `ps -ef | grep "$PWD/server.php"`
# `kill [enter-process-id]`

# --------------------------------
# you can also run
# php -S 0.0.0.0:8000 /laravel_app/server.php
# Get last background process PID
# PHP_SERVER_PID=$!

# Send SIGQUIT to php built-in server running in background to stop it
# kill -3 $PHP_SERVER_PID
# --------------------------------


# step: run dusk tests
php artisan dusk --stop-on-error --stop-on-failure

# end.

# -----------------

#cp /laravel_app/tests/Browser/screenshots/*.png /test_screenshots
#chmod -R 0755 /tests/Browser/screenshots/*.png
## rm /test_screenshots/*.png
#
#cp /webapp/webdriver.php /laravel_app/webdriver.php && cd /laravel_app && php webdriver.php
#rm /test_screenshots/*.png && cp /laravel_app/tests/Browser/screenshots/*.png /test_screenshots/

# php -S localhost.devv:8000

# external connections are allowed only from http://0.0.0.0:8000


# vim /laravel_app/tests/DuskTestCase.php

# check selenium access
# curl -v http://selenium:4444
# curl -v http://selenium:4444
# curl -I http://127.0.0.1:8000

## CLEANUP
# RUN apt-get update && apt-get upgrade -y && apt-get autoremove -y