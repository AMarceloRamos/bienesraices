# Usa la imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar dependencias necesarias para PostgreSQL y otras extensiones PHP
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html



# Copia el contenido del proyecto
COPY . .

# Ajustar permisos para Apache (importante en Render)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer el puerto 80 para Apache
EXPOSE 80

# Comando de inicio del servidor Apache
CMD ["apache2-foreground"]

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite
