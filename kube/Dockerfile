# 
# run from project root directory
# docker build -f kube/Dockerfile . -t ...
FROM composer:1.10 AS build
COPY . /app
RUN composer install --prefer-dist --no-progress --no-suggest

FROM php:7.2-fpm
MAINTAINER Astronaut apelttom@gmail.com
ENV TINI_VERSION v0.18.0

COPY kube/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql
ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /usr/local/bin/tini

COPY --from=build /app /var/www/orig
VOLUME ["/var/www/site"]
WORKDIR /var/www/site
# tini for legac
RUN chmod +x /usr/local/bin/tini \
    && chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /var/www/site/app \
    && chown -R 101 /var/www
ENTRYPOINT ["/usr/local/bin/tini", "--", "/usr/local/bin/docker-entrypoint.sh"]