ARG PHP_VERSION=8.3

FROM php:${PHP_VERSION}-fpm

# To use args inside the Docker script it must be set after the "FROM" section
# @see https://qmacro.org/blog/posts/2024/05/13/using-arg-in-a-dockerfile-beware-the-gotcha/
ARG SYS_USER_UID=1000
ARG SYS_USER_GID=1000
ARG SYS_USER_NAME=www-data
ARG APP_WORK_DIR=/var/www/app
ARG APP_COMPOSER_DIR=/var/www/.composer

WORKDIR ${APP_WORK_DIR}

# Set www-data to have UID 1000 and ensure the shell is bash
RUN usermod -u 1000 ${SYS_USER_NAME} && \
    chown -R ${SYS_USER_NAME}:${SYS_USER_NAME} ${APP_WORK_DIR} && \
    chmod -R 755 ${APP_WORK_DIR}

# Install system dependencies
RUN rm -rf /var/lib/apt/lists/* && apt-get update
RUN apt-get install -yqq --no-install-recommends \
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
    zlib1g-dev

# Install PHP extensions
RUN docker-php-ext-install opcache soap intl pcntl zip \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-source extract \
    && pecl install xdebug \
    && docker-php-source delete \
    && docker-php-ext-install opcache soap intl pcntl zip \
    && docker-php-ext-enable opcache sodium xdebug pcntl zip

# Clean up package cache to reduce image size
RUN rm -rf /var/lib/apt/lists/* /tmp/*

# Install Composer
RUN mkdir /var/www/.composer && \
    chown -R ${SYS_USER_NAME}:${SYS_USER_NAME} /var/www/.composer && \
    chmod -R 755 /var/www/.composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure GIT for the user
RUN git config --global --add safe.directory ${APP_WORK_DIR}
