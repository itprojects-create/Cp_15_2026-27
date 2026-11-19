#!/usr/bin/env bash

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Optimize Laravel
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
