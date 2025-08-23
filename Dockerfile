# Use official PHP + Apache image
FROM php:8.2-apache

# Install PostgreSQL PDO driver
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy all project files into Apache web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Tell Apache to use index.php as the default page
RUN echo "DirectoryIndex index.php" > /etc/apache2/conf-available/directoryindex.conf \
    && a2enconf directoryindex

# Enable Apache mod_rewrite (optional, useful for clean URLs)
RUN a2enmod rewrite

# Expose port 80 (Render will map this automatically)
EXPOSE 80
