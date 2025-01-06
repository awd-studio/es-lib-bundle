ARG PHP_VERSION
ARG uid

FROM php:${PHP_VERSION}-fpm

# Set www-data to have UID 1000
RUN usermod -u 1000 www-data;

RUN apt-get update -qq && apt-get install -yqq \
    curl \
    g++ \
    git \
    libxml2-dev \
    libicu-dev \
    libcurl4-gnutls-dev \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-dev \
    libpng-dev \
    libsodium-dev \
    libzip-dev \
    zip \
    zlib1g-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-source extract \
    && pecl install xdebug \
    && docker-php-source delete \
    && docker-php-ext-install opcache soap intl pcntl zip \
    && docker-php-ext-enable opcache sodium xdebug pcntl zip \
    && rm -rf /tmp/*

# Clean up package cache
RUN rm -rf /var/cache/apk/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cofnigure GIT
RUN git config --global --add safe.directory /var/www/app

CMD ["php-fpm", "-F"]

WORKDIR /var/www/app

EXPOSE 9000
