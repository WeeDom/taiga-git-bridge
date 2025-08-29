# Dockerfile for SlimPHP app
FROM php:8.2-apache
WORKDIR /var/www/html
COPY . /var/www/html/
RUN apt-get update && apt-get install -y unzip git \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install \
    && chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "<VirtualHost *:80>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n    </Directory>\n</VirtualHost>" > /etc/apache2/sites-available/000-default.conf
EXPOSE 80
CMD ["apache2-foreground"]
