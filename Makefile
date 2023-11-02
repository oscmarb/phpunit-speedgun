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

fix-cs:
		docker run --rm -v ${PWD}:/data cytopia/php-cs-fixer fix --verbose --show-progress=dots --rules=@Symfony,-@PSR2 -- src
		docker run --rm -v ${PWD}:/data cytopia/php-cs-fixer fix --verbose --show-progress=dots --rules=@Symfony,-@PSR2 -- tests

validate-cs:
		docker run --rm -v ${PWD}:/data cytopia/php-cs-fixer fix --dry-run --verbose --show-progress=dots --rules=@Symfony,-@PSR2 -- src
		docker run --rm -v ${PWD}:/data cytopia/php-cs-fixer fix --dry-run --verbose --show-progress=dots --rules=@Symfony,-@PSR2 -- tests

.PHONY: tests
tests:
	make ci-tests
	make phpunit

ci-tests:
	make validate-cs
	make phpstan
