#!/bin/sh
set -eux

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'bin/console' ]; then
	PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-production"
	if [ "$APP_ENV" != 'prod' ]; then
		PHP_INI_RECOMMENDED="$PHP_INI_DIR/php.ini-development"
	fi
	ln -sf "$PHP_INI_RECOMMENDED" "$PHP_INI_DIR/php.ini"

    mkdir -p var/cache var/log var/db/${APP_ENV} var/assets

    if [ "$APP_ENV" = 'prod' ]; then
      rm -rf var/cache
      bin/console secrets:decrypt-to-local --force --env=prod
      bin/console cache:warm
    # Modification >>>
    elif [ "$APP_ENV" != 'prod' ]; then
        composer install --prefer-dist --no-progress --no-interaction
    fi

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
