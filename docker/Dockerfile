FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_sqlite bcmath

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY 90-xdebug.ini /usr/local/etc/php/conf.d
# Coverage
# COPY 90-xdebug-coverage.ini /usr/local/etc/php/conf.d

WORKDIR /var/www/html

COPY . .
