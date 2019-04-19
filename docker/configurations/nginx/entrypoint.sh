#!/usr/bin/env sh

set -eu

envsubst '${NGINX_SERVER_NAME} ${NGINX_SSL_CRT_FILE_NAME} ${NGINX_SSL_KEY_FILE_NAME}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf;

exec nginx -g 'daemon off;'
