FROM composer:2.0 as composer

COPY src /app/src
COPY bin /app/bin
COPY composer.json /app/composer.json
COPY composer.lock /app/composer.lock

RUN composer install -d /app -n --no-dev --no-scripts --no-progress


FROM php:8.0

COPY --from=composer /app /sqldoc

RUN ln -s /sqldoc/bin/sqldoc /usr/local/bin/sqldoc

ENTRYPOINT ["/usr/local/bin/sqldoc"]
