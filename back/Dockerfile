# Utilisons l'image PHP 8.3 avec FPM
FROM php:8.3-fpm

# Dépendances nécessaires
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libssl-dev \
    librabbitmq-dev \
    # Installe amqp via PECL
    && pecl install amqp \
    # Active le module amqp
    && docker-php-ext-enable amqp\
    # Nettoie les fichiers inutiles après installation
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Crée le répertoire de travail
WORKDIR /var/www/html

# Copie les fichiers de l'application dans l'image Docker
COPY . /var/www/html

# Change le propriétaire et le groupe des fichiers, puis applique les bonnes permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

RUN chown -R www-data:www-data var/ && chmod -R 755 var/

#Installer toutes les dépendances du projet
RUN composer install --optimize-autoloader
# --no-dev

# Expose le port 9000 (pour PHP-FPM)
EXPOSE 9000

# Démarre PHP-FPM
CMD ["php-fpm"]
