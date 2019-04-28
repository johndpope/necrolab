FROM necrolab-base-cli:1.0

# Configure Supervisor service file paths
ENV SUPERVISOR_CONF_DIR=/etc/supervisor.d
ENV SUPERVISOR_CONF_FILE=/etc/supervisord.conf

USER root

# Install all packages unique to queue workers
RUN apk update && apk upgrade \
    && apk add --no-cache supervisor \
    && apk add --no-cache mono --repository http://dl-cdn.alpinelinux.org/alpine/edge/testing \
    && mkdir -p $SUPERVISOR_CONF_DIR

VOLUME $SUPERVISOR_CONF_DIR

ENTRYPOINT supervisord -n -c $SUPERVISOR_CONF_FILE
