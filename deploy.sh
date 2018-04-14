#!/bin/sh

#user group
chown -R www:www $PWD

#directory permission
chmod -R 777 app config public resources storage vendor

#project clear
php artisan view:clear
php artisan cache:clear

#project rebuild
php artisan optimize
php artisan config:cache
php artisan route:cache