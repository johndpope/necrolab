FROM necrolab-base-cli:1.0

ENV APP_USER necrolab_cron

COPY ./crontab /var/spool/cron/crontabs/$APP_USER

# Install temporary build dependencies
RUN adduser -g "App User" -D $APP_USER \
    && chmod 0600 /var/spool/cron/crontabs/$APP_USER
    
ENTRYPOINT "crond"

CMD ["-f", "-d", "8"]
