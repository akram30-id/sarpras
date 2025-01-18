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

# Create the .env file
RUN echo "\
#APP\n\
SARPRAS_ENV=development\n\
SARPRAS_BASE_URL=http://sarpras.test/\n\
\n\
#DB\n\
SARPRAS_DEFAULT_DBHOSTNAME=certixdb.cj04oa2g0ew8.ap-southeast-1.rds.amazonaws.com\n\
SARPRAS_DEFAULT_DBUSERNAME=root\n\
SARPRAS_DEFAULT_DBPASSWORD=Sm4rtCod3x$\n\
SARPRAS_DEFAULT_DBNAME=sarpras\n\
SARPRAS_DEFAULT_DBPORT=3306\n\
" > /var/www/html/.env

# Create the .htaccess file
RUN echo "\
<IfModule mod_rewrite.c>\n\
    RewriteEngine On\n\
    RewriteCond %{REQUEST_FILENAME} !-f\n\
    RewriteCond %{REQUEST_FILENAME} !-d\n\
    RewriteRule ^(.*)$ index.php/$1 [L]\n\
</IfModule>\n\
" > /var/www/html/.htaccess

# Create the api_log directory and set permissions
RUN mkdir -p /var/www/html/api_log && \
    chown -R www-data:www-data /var/www/html/api_log && \
    chmod -R 775 /var/www/html/api_log

# Ensure proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Configure Apache to use the .htaccess file
RUN echo "<Directory /var/www/html>\n\
    AllowOverride All\n\
</Directory>" >> /etc/apache2/apache2.conf

# Expose port 9001 instead of the default Apache port
EXPOSE 9001

# Update Apache to listen on port 9001
RUN sed -i 's/Listen 80/Listen 9001/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:9001>/' /etc/apache2/sites-enabled/000-default.conf

# Start Apache in the foreground
CMD ["apache2-foreground"]
