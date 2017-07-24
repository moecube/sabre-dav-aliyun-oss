FROM php:fpm

RUN apt-get update
RUN apt-get install -y unzip git libpq-dev

RUN curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo_pgsql

RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

COPY composer.json /usr/src/app/
COPY composer.lock /usr/src/app/

RUN composer install

COPY . /usr/src/app