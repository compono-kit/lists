FROM php:8.0-alpine

ENV XDEBUG_VERSION=3.4.2

RUN apk add --update --no-cache linux-headers icu-dev icu-data-full $PHPIZE_DEPS \
	&& docker-php-ext-install intl \
	&& pecl install xdebug-${XDEBUG_VERSION} \
	&& docker-php-ext-enable xdebug \
	&& apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENTRYPOINT ["composer"]
