FROM necrolab-base-cli:1.0

USER root

COPY ./docker/configurations/crontab/crontab /var/spool/cron/crontabs/www

RUN chmod 0600 /var/spool/cron/crontabs/www

USER www
    
ENTRYPOINT "crond"

CMD ["-f", "-d", "8"]
