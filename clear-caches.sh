#!/bin/bash
echo "Clearing Laravel caches..."

# Clear Laravel configuration cache
php artisan config:clear

# Clear Laravel application cache
php artisan cache:clear

# Clear Laravel route cache
php artisan route:clear

# Clear Laravel view cache
php artisan view:clear



php artisan cache:clear
php artisan route:cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan view:cache
php artisan event:clear
php artisan event:cache
php artisan optimize:clear
npm cache clean --force
composer clear-cache
# Regenerate Composer autoload files
composer dump-autoload
php artisan package:discover

echo "All caches cleared successfully!"
