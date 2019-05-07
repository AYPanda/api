FROM php:7.2-fpm

RUN ln -sf /usr/share/zoneinfo/Europe/Moscow /etc/localtime

RUN apt-get update \
&& apt-get install -y git \
                      cron \
                      zlib1g-dev \
&& pecl install xdebug-2.6.1 \
&& docker-php-ext-enable xdebug \
&& docker-php-ext-install pdo pdo_mysql zip bcmath \
&& curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer


WORKDIR /var/www/html

COPY composer.json ./
COPY /docker/php/php.ini /usr/local/etc/php/php.ini

RUN usermod -u 1000 www-data
RUN composer install

EXPOSE 9000