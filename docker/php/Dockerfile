# Use the Ubuntu 24.04 as the base image
FROM public.ecr.aws/docker/library/ubuntu:24.04
# Set environment variables
ENV LANG=C.UTF-8
ENV LC_ALL=C.UTF-8
ENV TZ=Asia/Tokyo
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer

# Copy Composer binary from the official Composer Docker image
COPY --from=public.ecr.aws/docker/library/composer:latest /usr/bin/composer /usr/bin/composer

# Install system dependencies
RUN apt update \
&& apt -y install --no-install-recommends tzdata \
&& apt -y install --no-install-recommends \
   git \
   libicu-dev \
   libonig-dev \
   libpng-dev \
   libpq-dev \
   libxml2-dev \
   libzip-dev \
   unzip \
   zip \
   curl \
   ca-certificates \
   php8.3-bcmath \
   php8.3-cli \
   php8.3-common \
   php8.3-curl \
   php8.3-fpm \
   php8.3-gd \
   php8.3-mbstring \
   php8.3-mysql \
#   php8.3-pdo \
   php8.3-xml \
   php8.3-zip \
   libmagickwand-dev \
   php8.3-imagick \
&& apt clean \
&& rm -rf /var/lib/apt/lists/* \
&& composer config -g process-timeout 3600 \
&& composer config -g repos.packagist composer https://packagist.org

# Install nodejs and npm
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash
RUN apt -y install nodejs

# Create a directory for the app
RUN mkdir -p /var/www/inertia

# Copy the app files to the container
COPY . /var/www/inertia
COPY ./docker/php/php.ini /etc/php/8.3/fpm/
COPY ./docker/php/php-fpm.conf /etc/php/8.3/fpm/
COPY ./docker/php/www.conf /etc/php/8.3/fpm/pool.d/

# Set the working directory to the app directory
WORKDIR /var/www/inertia

# Install application dependencies, set the ownership, permission and optimize
RUN composer install -q -n --no-ansi --no-dev --no-scripts --no-progress --prefer-dist \
&& chown -R www-data:www-data /var/www/inertia \
&& chmod -R 775 storage bootstrap/cache

# Start PHP-FPM Server
CMD [ "/usr/sbin/php-fpm8.3" ]