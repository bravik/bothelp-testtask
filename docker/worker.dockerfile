FROM php:7.4-cli

RUN docker-php-ext-install sockets

RUN apt-get update && apt-get install \
    zip -y

# Composer
RUN curl -sS https://getcomposer.org/installer | tee composer-setup.php \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer

WORKDIR /app

ADD ./composer.json /app/composer.json

RUN composer install
