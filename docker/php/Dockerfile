FROM php:7.4-fpm

RUN docker-php-ext-install pdo_mysql

RUN apt-get update && \
apt-get install -y \
zlib1g-dev \
wget \
git \
procps \
libzip-dev \
zip \
unzip

RUN docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

RUN wget -O phpunit https://phar.phpunit.de/phpunit-9.phar \
    && mv phpunit /usr/local/bin/ \
    && chmod +x /usr/local/bin/phpunit

WORKDIR /usr/src/app

COPY app /usr/src/app

COPY ./docker/php/docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
