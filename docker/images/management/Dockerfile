FROM necrolab-base-cli:1.0

USER root

# Install management dependencies
RUN apk add --no-cache \
    shadow \
    ncurses \
    vim \
    nodejs \
    nodejs-npm \
    bash \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER www
