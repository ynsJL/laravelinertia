# Set the base image to Ubuntu 24.04
FROM public.ecr.aws/docker/library/ubuntu:24.04

# Set environment variables
ENV LANG=C.UTF-8
ENV LC_ALL=C.UTF-8
ENV TZ=Asia/Tokyo

# Update package index and install necessary packages
RUN apt update \
&& apt -y install --no-install-recommends tzdata \
&& apt -y install --no-install-recommends apache2

# Copy project files to container
COPY . /var/www/l-track

# Copy Apache configuration files
COPY ./docker/httpd/apache2.conf /etc/apache2/
COPY ./docker/httpd/security.conf /etc/apache2/conf-available/
COPY ./docker/httpd/vhosts.conf /etc/apache2/sites-available/

# Enable Apache modules and configuration files
RUN a2ensite vhosts
RUN a2enmod headers proxy proxy_fcgi remoteip rewrite

# Disable Apache configuration files
RUN a2disconf other-vhosts-access-log serve-cgi-bin
RUN a2dissite 000-default

# Set owner of web files to Apache user
RUN chown -R www-data:www-data /var/www/l-track

# Start Apache server
CMD [ "/usr/sbin/apachectl", "-D", "FOREGROUND" ]