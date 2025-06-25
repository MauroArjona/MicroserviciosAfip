FROM php:8.2-fpm

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring soap

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear directorio de trabajo
WORKDIR /var/www

# Copiar la app
COPY . .

# Instalar dependencias Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permisos correctos
RUN chown -R www-data:www-data /var/www

# Exponer puerto 80 (para que coincida con tu YAML)
EXPOSE 80

# Ejecutar Laravel en el puerto 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
