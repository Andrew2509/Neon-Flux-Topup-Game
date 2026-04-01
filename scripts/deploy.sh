#!/bin/bash
set -e

echo "🚀 Starting Deployment..."

# 1. Pull the latest change (if using Git)
# git pull origin main

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Install NPM dependencies & Build assets
npm install
npm run build

# 4. Clear/Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run Database Migrations
php artisan migrate --force

# 6. Symlink storage & Permissions
php artisan storage:link
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# 7. Restart Supervisor (optional)
# sudo supervisorctl restart laravel-worker:*

echo "✅ Deployment Successful!"
