FROM php:8.4-fpm

ARG UID=1000
ARG GID=1000

RUN groupmod -g $GID www-data && usermod -u $UID -g $GID www-data

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev

RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath

RUN apt-get update && apt-get install -y libsqlite3-dev
RUN apt-get update && apt-get install -y libsqlite3-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN mkdir -p storage bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache /tmp

# RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]