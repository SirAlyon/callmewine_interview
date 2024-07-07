FROM php:8.3-fpm-alpine


RUN apk update && apk add \
    git \
    curl \
    zip \
    unzip


RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && apk --no-cache add nodejs npm

# Install PHP extension dependencies
RUN apk --no-cache add libintl icu-dev icu-libs

# Install and enable the intl extension
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


