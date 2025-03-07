# Usa la imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar dependencias necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia todo el contenido del proyecto
COPY . .

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio del servidor Apache
CMD ["apache2-foreground"]
