FROM necrolab-base-web:1.0

USER root

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Override with custom opcache settings
COPY configurations/php/opcache.ini $PHP_INI_DIR/conf.d/

USER www-data
