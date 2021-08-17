FROM php:8.0-fpm-alpine as s_php

ENV DOCUMENT_ROOT /var/www/symfony/
WORKDIR ${DOCUMENT_ROOT}

RUN apk update

# postgres
RUN apk add postgresql-dev
RUN docker-php-ext-install -j$(nproc) pdo_pgsql;
RUN docker-php-ext-configure pcntl --enable-pcntl

# ext-install
RUN docker-php-ext-install opcache pcntl

# apks
RUN apk add libzip-dev curl-dev
RUN apk add alpine-sdk openssl-dev php8-dev php8-pear
RUN apk add pcre-dev git
RUN apk add net-tools nmap
RUN apk add util-linux pciutils usbutils coreutils binutils findutils grep

#ARG APCU_VERSION=5.1.20
#RUN pecl install apcu-${APCU_VERSION};
#RUN	pecl clear-cache;
#RUN docker-php-ext-enable \
#     		apcu \
#     		opcache \
#    ;

RUN rm -rf /var/cache/apk/*

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer

# XDEBUG
RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo 'xdebug.idekey=PHPSTORM' >> /usr/local/etc/php/php.ini

# v3
RUN echo 'xdebug.mode=debug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.connect_timeout_ms=1000' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.start_with_request=true' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.xdebug.discover_client_host=false' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_host=xdebug.lan' >> /usr/local/etc/php/php.ini
#this is contraversial - it will disable all errors
RUN echo 'xdebug.log_level=0' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.log=/tmp/xdebug_remote.log' >> /usr/local/etc/php/php.ini

RUN echo "alias xdon=export XDEBUG_MODE=debug" >> ~/.bash_profile
RUN echo "alias xo=export XDEBUG_MODE=debug" >> ~/.bash_profile
RUN echo "alias xdoff=export XDEBUG_MODE=off" >> ~/.bash_profile
RUN echo "alias xf=export XDEBUG_MODE=off" >> ~/.bash_profile

EXPOSE 9003
