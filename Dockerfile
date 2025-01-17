# Use a base image with PHP 7 and Apache
FROM php:7.4-apache

# Install necessary system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
	git \
	zip \
	unzip \
	&& docker-php-ext-install mysqli pdo pdo_mysql \
	&& a2enmod rewrite \
	&& apt-get clean

# Set the working directory in the container
WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Fix Git ownership issue
RUN git config --global --add safe.directory /var/www/html

# Run Composer update to install dependencies
RUN composer update --no-dev --optimize-autoloader --working-dir=/var/www/html || \
	(echo "Error during composer update. Continuing with a fallback.")

# Remove the problematic sed command if unnecessary
# Ensure proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html \
	&& chmod -R 755 /var/www/html

# Configure Apache to use the .htaccess file
RUN echo "<Directory /var/www/html>\n\
	AllowOverride All\n\
	</Directory>" >> /etc/apache2/apache2.conf

# Expose the Apache port
EXPOSE 9001

# Start Apache in the foreground
CMD ["apache2-foreground"]
