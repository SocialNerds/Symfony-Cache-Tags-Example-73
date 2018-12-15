FROM php:7.2-fpm

# Get user id and group id from arguments.
ARG UID
ARG GID

# Add user.
RUN sed -i -e"s/^user\s=\swww-data/user = serveruser/" /usr/local/etc/php-fpm.d/www.conf && \
  sed -i -e"s/^group\s=\swww-data/group = serveruser/" /usr/local/etc/php-fpm.d/www.conf && \
  sed -i -e"s/^;php_admin_value\[memory_limit\]\s=\s32M/php_admin_value[memory_limit] = 256M/" /usr/local/etc/php-fpm.d/www.conf && \
  echo "\ncatch_workers_output = yes\nphp_admin_value[error_log] = /var/log/fpm-php.www.log\nphp_admin_flag[log_errors] = on\n" >> /usr/local/etc/php-fpm.d/www.conf && \
  touch /var/log/fpm-php.www.log && chmod 777 /var/log/fpm-php.www.log && \
  groupadd -r serveruser -g $GID && useradd -ms /bin/bash serveruser -u $UID -g $GID

RUN docker-php-ext-install pdo_mysql

# GD.
RUN apt-get update && \
  apt-get install -y \
  libpng-dev git wget nano netcat zip unzip mysql-client && \
docker-php-ext-install gd

# Opcache.
RUN docker-php-ext-configure opcache --enable-opcache && \
  docker-php-ext-install opcache

# Redis.
RUN pecl install redis-4.1.1 && \
  docker-php-ext-enable redis

# Install composer.
RUN cd ~
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

RUN echo "PATH=$PATH:~/.composer/vendor/bin" >> ~/.bashrc

# Install phpunit.
RUN wget https://phar.phpunit.de/phpunit.phar
RUN chmod +x phpunit.phar
RUN mv phpunit.phar /usr/local/bin/phpunit

WORKDIR /home/serveruser

ENTRYPOINT ["php-fpm"]