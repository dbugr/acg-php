FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libxml2-dev \
    libonig-dev \
    libxslt-dev \
    libzip-dev \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions not present in the base image
# (dom, simplexml, xmlreader, xmlwriter, xml, mbstring, tokenizer,
#  ctype, fileinfo, posix, pdo, phar are already enabled in php:7.4-apache)
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
 && docker-php-ext-install gd
RUN docker-php-ext-install mysqli pdo_mysql
RUN docker-php-ext-install xsl exif sockets shmop gettext calendar
RUN docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the site root
ENV SITE_ROOT=/shared/httpd/acg

# Copy site files into the container
COPY lib/ ${SITE_ROOT}/lib/
COPY public_html/ ${SITE_ROOT}/public_html/
COPY var/ ${SITE_ROOT}/var/

# Install PHP dependencies (adds mailgun + amphp into lib/vendor/)
WORKDIR ${SITE_ROOT}/lib
RUN composer update --no-interaction --no-dev --optimize-autoloader

# Create writable runtime directories
RUN mkdir -p ${SITE_ROOT}/var/session \
             ${SITE_ROOT}/var/log \
             ${SITE_ROOT}/var/photos \
 && chown -R www-data:www-data ${SITE_ROOT}/var \
 && chmod -R 755 ${SITE_ROOT}/var

WORKDIR /var/www/html
