# ============================================
# APROTEC — Dockerfile para Dokploy
# PHP 8.2 + Apache con mod_rewrite
# ============================================
FROM php:8.2-apache

# Habilitar mod_rewrite para las rutas de Laminas
RUN a2enmod rewrite

# Instalar utilidades y extensiones PHP requeridas por Laminas y Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_mysql zip

# Copiar configuración de Apache para el virtual host
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar el proyecto completo
COPY . .

# Usar configuración de BD para Docker (sobreescribe el local.php de XAMPP)
RUN cp /var/www/html/config/autoload/local.php.docker /var/www/html/config/autoload/local.php

# Instalar Composer y dependencias de producción
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Reemplazar .htaccess local con el de producción (RewriteBase /)
RUN cp /var/www/html/docker/htaccess.prod /var/www/html/public/.htaccess

# Permisos correctos para Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && mkdir -p /var/www/html/data/cache \
    && mkdir -p /var/www/html/data/session \
    && chown -R www-data:www-data /var/www/html/data

# Puerto expuesto
EXPOSE 80
