# Use official PHP + Apache image
FROM php:8.2-apache

# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files into Apache web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Tell Apache to use home.php as the default page
RUN echo "DirectoryIndex home.php" > /etc/apache2/conf-available/directoryindex.conf \
    && a2enconf directoryindex

# Expose port 80 (Render will map this automatically)
EXPOSE 80
