#!/usr/bin/env bash
echo "Removing old image..."
docker rmi necrolab-app-development:latest
echo "Building the development image..."
docker build . -f ./docker/images/full-app/Dockerfile -t necrolab-app-development:latest --no-cache
