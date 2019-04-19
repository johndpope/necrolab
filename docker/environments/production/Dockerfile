FROM php:7.3-fpm-alpine

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

RUN apk update && apk upgrade

# Install temporary build dependencies
RUN apk add --no-cache --virtual .build-dependencies \
    $PHPIZE_DEPS \
    build-base \
    curl-dev \
    libtool \
    libxml2-dev \
    postgresql-dev \
    zlib-dev

# Install dependencies
RUN apk add \
    shadow \
    ncurses \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libzip-dev \
    postgresql-libs \
    jpegoptim optipng pngquant gifsicle \
    vim \
    git \
    curl \
    nodejs \
    nodejs-npm \
    bash \
    zip \
    unzip

# Install extensions
RUN docker-php-ext-install opcache pdo_pgsql pdo_mysql soap xmlrpc exif pcntl
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install zip
RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
RUN docker-php-ext-install gd

RUN git clone https://github.com/phpredis/phpredis.git /tmp/phpredis \
    && cd /tmp/phpredis \
    && git checkout -b 4.3.0 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && cd ../ \
    && rm -rf /tmp/phpredis

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove the build dependencies
RUN apk del -f .build-dependencies

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Run composer install
#RUN composer install --no-interaction

# Start php-fpm server
CMD ["php-fpm"]
