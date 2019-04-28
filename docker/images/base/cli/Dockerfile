FROM php:7.3-cli-alpine

# Set working directory
WORKDIR /var/www

# These steps are compressed into one RUN statement to minimize the layers created for this image which reduces its size.
# 1. Install temporary build dependencies
# 2. Install dependencies
# 3. Install PHP extensions
# 4. Install phpredis and enable it
# 5. Add the www user and its group
# 6. Remove the build dependencies
RUN apk update && apk upgrade \
    && apk add --no-cache --virtual .build-dependencies \
        $PHPIZE_DEPS \
        build-base \
        curl-dev \
        libtool \
        libxml2-dev \
        postgresql-dev \
        zlib-dev \
        git \
        shadow \
    && apk add --no-cache \
        ca-certificates \
        openssl \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        postgresql-libs \
        jpegoptim optipng pngquant gifsicle \
        curl \
        zip \
        unzip \
    && apk add --no-cache mono --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing \
    && docker-php-ext-install opcache pdo_pgsql pdo_mysql soap xmlrpc exif pcntl \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && git clone https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && git checkout -b 4.3.0 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && cd ../ \
    && rm -rf /tmp/phpredis \
    && docker-php-ext-enable redis \
    && groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www \
    && apk del -f .build-dependencies \
    && cert-sync /etc/ssl/certs/ca-certificates.crt

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

USER www
