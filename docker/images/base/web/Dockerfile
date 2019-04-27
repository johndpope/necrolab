FROM php:7.3-fpm-alpine

# Set working directory
WORKDIR /var/www

# These steps are compressed into one RUN statement to minimize the layers created for this image which reduces its size.
# 1. Install temporary build dependencies
# 2. Install dependencies
# 3. Install PHP extensions
# 4. Install phpredis and enable it
# 5. Sync CA certificates
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
    && apk add --no-cache \
        ca-certificates \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        postgresql-libs \
        jpegoptim optipng pngquant gifsicle \
        curl \
        zip \
        unzip \
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
    && apk del -f .build-dependencies \
    && chown -R www-data:www-data /var/www

# Start php-fpm server
CMD ["php-fpm"]
