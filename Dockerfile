# Use a base image with PHP 7 and Apache
FROM php:7.4-apache

# Install necessary extensions for CodeIgniter
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite for .htaccess support
RUN a2enmod rewrite

# Set the working directory in the container
WORKDIR /var/www/html

# Copy CodeIgniter project files to the container
COPY . /var/www/html

# Ensure proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html \
	&& chmod -R 755 /var/www/html

# Configure Apache to use the .htaccess file
RUN echo "<Directory /var/www/html>\n\
	AllowOverride All\n\
	</Directory>" >> /etc/apache2/apache2.conf

# Expose port 9001 instead of the default Apache port
EXPOSE 80

# Update Apache to listen on port 9001
RUN sed -i 's/Listen 80/Listen 9001/' /etc/apache2/ports.conf \
	&& sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:9001>/' /etc/apache2/sites-enabled/000-default.conf

# Start Apache in the foreground
CMD ["apache2-foreground"]
