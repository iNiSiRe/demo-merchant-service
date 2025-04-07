FROM php:8.2-fpm as base

USER root

RUN sed -i '/jessie-updates/d' /etc/apt/sources.list

RUN apt-get -qq update

RUN export DEBIAN_FRONTEND=noninteractive && \
    apt-get -y install vim telnet tcptraceroute traceroute git zip unzip

# add optimized php.ini and remove xdebug
COPY ./.ci/docker/php-fpm/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./.ci/docker/php-fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./.ci/docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN rm -f /usr/local/etc/php/conf.d/*xdebug.ini && docker-php-ext-enable opcache

# install extensions
RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install bcmath

# install composer
ENV COMPOSER_HOME=/tmp/.composer
RUN curl -XGET https://getcomposer.org/installer > composer-setup.php && \
    php composer-setup.php --install-dir=/bin --filename=composer --version=1.3.0 && \
    rm composer-setup.php

RUN rm -rf /var/www/html

RUN usermod -u 1000 www-data  \
    && groupmod -g 1000 www-data \
    && chown www-data:www-data -R /var/www/html

USER www-data

CMD ["php-fpm", "-R"]

FROM base as artifact

# Add sources
ADD --chown=www-data:www-data . /var/www/html/

USER www-data

# Install deps
#RUN --mount=type=cache,target=/tmp/.composer/,mode=777,uid=1000,gid=1000,sharing=locked \ omg, can't use cache because of outdated docker version
RUN APP_ENV=prod composer install --no-dev --ignore-platform-reqs --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction \
    && APP_ENV=prod composer dump-autoload --optimize