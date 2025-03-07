# Usa la imagen oficial de PHP con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias para PDO y PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Instalar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia todo el contenido de tu proyecto
COPY . .

# Exponer el puerto 80
EXPOSE 80

# Comando de inicio (puede ajustarse según tus necesidades)
CMD ["apache2-foreground"]
