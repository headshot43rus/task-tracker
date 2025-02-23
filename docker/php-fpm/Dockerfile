FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    libxml2-dev \
    && docker-php-ext-install \
    ctype \
    iconv \
    json \
    pdo_mysql \
    session \
    simplexml \
    tokenizer \
    xml \
    zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/symfony

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/symfony/var

CMD ["php-fpm"]