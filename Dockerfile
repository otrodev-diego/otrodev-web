# Usar una imagen base de PHP 8 con Apache
FROM php:8.2-apache

# Instalar dependencias necesarias (PostgreSQL, extensiones de PHP, Composer, etc.)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zlib1g-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-install zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configuración de Apache (habilitar mod_rewrite)
RUN a2enmod rewrite

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar los archivos del proyecto al contenedor
COPY . .

# Establecer permisos para los directorios de almacenamiento y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80 para el servidor web
EXPOSE 80

# Instalar las dependencias de Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ejecutar el servidor Apache
CMD ["apache2-foreground"]
