#!/bin/bash

composer install

php-fpm

exec "$@"