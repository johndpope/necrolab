#!/usr/bin/env bash
echo "========== Removing old images =========="
docker rmi necrolab-base-cli:1.0
docker rmi necrolab-base-web:1.0
docker rmi necrolab-app-production:1.0
docker rmi necrolab-app-cron:1.0
docker rmi necrolab-queue-worker:1.0
docker rmi necrolab-management:1.0
echo "========== Building the base images =========="
docker build docker/ -f ./docker/images/base/cli/Dockerfile -t necrolab-base-cli:1.0 --no-cache
docker build docker/ -f ./docker/images/base/web/Dockerfile -t necrolab-base-web:1.0 --no-cache
echo "========== Building the app image =========="
docker build docker/ -f ./docker/images/php-web-app/Dockerfile -t necrolab-app-production:1.0 --no-cache
echo "========== Building the cron image =========="
docker build docker/configurations/crontab/ -f ./docker/images/cron-server/Dockerfile -t necrolab-app-cron:1.0 --no-cache
echo "========== Building the queue worker image =========="
docker build docker/ -f ./docker/images/queue-worker/Dockerfile -t necrolab-queue-worker:1.0 --no-cache
echo "========== Building the management image =========="
docker build docker/ -f ./docker/images/management/Dockerfile -t necrolab-management:1.0 --no-cache
