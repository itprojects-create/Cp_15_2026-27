# Stage 1: Install Composer dependencies
FROM composer:2.7 AS composer-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# Stage 2: Build frontend assets
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=composer-builder /app/vendor ./vendor
RUN npm run build

# Stage 3: Production environment
FROM richarvey/nginx-php-fpm:3.1.6
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy built frontend assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Copy vendor folder from composer-builder
COPY --from=composer-builder /app/vendor ./vendor

# Run composer dump-autoload to optimize the autoloader
RUN composer dump-autoload --optimize

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

# Make sure scripts are executable
RUN chmod +x /var/www/html/scripts/run.sh

CMD ["/start.sh"]
