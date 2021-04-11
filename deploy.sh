#!/usr/bin/env bash

set -eu

DOCKER_COMPOSE="docker-compose -f docker-compose.yml -f docker-compose.prod.yml"
${DOCKER_COMPOSE} pull

${DOCKER_COMPOSE} up -d --no-build --remove-orphans --force-recreate

