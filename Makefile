UID=$(shell id -u)
GID=$(shell id -g)
DOCKER_PHP_SERVICE=php

SHELL=/bin/bash

.DEFAULT_GOAL := start

start: build composer-install

build:
		docker build -t speed_gun .

erase:
		docker rmi speed_gun

composer-install:
		docker run --rm -v .:/app speed_gun composer install

composer-update:
		docker run --rm -v .:/app speed_gun composer update

phpunit:
		docker run --rm -v .:/app speed_gun vendor/bin/phpunit tests

phpstan:
		docker run --rm -v ${PWD}:/app ghcr.io/phpstan/phpstan analyse ./src -l 8
