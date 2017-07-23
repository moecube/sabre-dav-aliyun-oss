FROM php:fpm

RUN apt-get update
RUN apt-get install -y unzip git

RUN curl https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /usr/src/app
WORKDIR /usr/src/app

COPY composer.json /usr/src/app/
COPY composer.lock /usr/src/app/

RUN composer install

COPY . /usr/src/app