#!/usr/bin/env bash
set -e

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Instalar dependencias
composer install --no-dev --optimize-autoloader
