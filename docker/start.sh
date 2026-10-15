#!/bin/sh
set -e

# Render fournit le port à écouter dans $PORT
PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html

php artisan package:discover --ansi
php artisan config:cache
php artisan view:cache

# Base de données : tables + catégories (le seeder est rejouable sans doublon)
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force

exec apache2-foreground
