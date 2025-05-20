FROM php:8.3-fpm

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setea el directorio de trabajo
WORKDIR /var/www

# Copia el proyecto
COPY . .

# Copia el .env adecuado
COPY .env.docker .env

# Copia el entrypoint
COPY entrypoint.sh /entrypoint.sh

# Da permisos de ejecución al entrypoint
RUN chmod +x /entrypoint.sh

# Instala dependencias PHP
RUN composer install --no-interaction --prefer-dist

# Permisos
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Usa el entrypoint
ENTRYPOINT ["/entrypoint.sh"]
