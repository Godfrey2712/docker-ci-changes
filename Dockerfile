# Used from: https://hub.docker.com/_/php?tab=tags&page=1&ordering=last_updated
FROM php:8.0.9-fpm-alpine3.13

# Update the index of available packages
RUN apk update

# Install Python 2
RUN apk add --no-cache python2

# Install Bash
RUN apk add --no-cache bash

# Install pacman apk package

RUN wget -O /usr/local/bin/pacapt https://github.com/icy/pacapt/raw/ng/pacapt

RUN chmod 755 /usr/local/bin/pacapt

RUN ln -sv /usr/local/bin/pacapt /usr/local/bin/pacman || true

# update pacman package database and then upgrade the system
RUN pacman -Syu

# Install and update items for everything to run inclduing docker
RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
  gnupg zip unzip git wget libmcrypt curl && \
  docker-php-ext-configure gd \
  && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} gd pcntl && \
  apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# Install Lastest Node
RUN pacman -S nodejs npm

# Copy and Install NPM
COPY package.json ./
RUN npm install -g

# Install Gulp
RUN npm install -g gulp

# Install Gulp Sass
RUN npm rebuild -g node-sass
RUN npm rebuild node-sass

# Specify Composer Versions
ENV php_codesniffer=3.7.* php_compatibility=9.3.* wpcs=2.3.* phpcs_variable_analysis=2.11.* phpcodesniffer_composer_installer=0.7.*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Allow for root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Check for Composer Updates
RUN composer self-update --2

# Allow from Composer
RUN composer global config --no-plugins allow-plugins.dealerdirect/phpcodesniffer-composer-installer true

# Install from Composer
RUN composer global require \
"squizlabs/php_codesniffer=$php_codesniffer" \
"phpcompatibility/php-compatibility=$php_compatibility" \
"wp-coding-standards/wpcs=$wpcs" \
"dealerdirect/phpcodesniffer-composer-installer=$phpcodesniffer_composer_installer" \
"sirbrillig/phpcs-variable-analysis=$phpcs_variable_analysis"

# Check versions after complete
RUN node -v
RUN npm -v
RUN composer -V
RUN gulp -v
RUN composer global show
RUN npm list -g --depth 0
RUN npm ls -g --depth 1

# Make new dir in docker image
RUN mkdir -p /docker_ci

# Add the content of the common_ci to the docker dir
# To be consumed with /docker_ci/{name_of_ci_file}
ADD common_ci/ /docker_ci

# List all dir and files to be sure it copied 
# This would be seen only when a new a new file is added or a new change is made at buildstage.
RUN ls -laR ./*
RUN ls -laR /*
