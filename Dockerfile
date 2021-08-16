# Use php 7.2
FROM php:7.4.11-fpm-alpine3.11

# Specify Node * NPM Versions
ENV NODE_VERSION=15.7.0 NPM_NATIVES=1.1.6

# Specify Composer Versions
ENV php_codesniffer=3.5.* php_compatibility=9.3.* wpcs=2.3.* phpcodesniffer_composer_installer=0.7.* phpcs_variable_analysis=2.9.*

# Update the index of available packages
RUN apk update

# Install Python 2
RUN apk add python2

RUN apk add --no-cache bash

# Install pacman apk package

RUN wget -O /usr/local/bin/pacapt https://github.com/icy/pacapt/raw/ng/pacapt

RUN chmod 755 /usr/local/bin/pacapt

RUN ln -sv /usr/local/bin/pacapt /usr/local/bin/pacman || true

# update pacman package database and then upgrade the system
RUN pacman -Syu

# Install and update items for everything to run inclduing docker
RUN apk add --no-cache freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev \
  gnupg zip unzip git wget libmcrypt curl python && \
  docker-php-ext-configure gd \
  && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} gd pcntl && \
  apk del --no-cache freetype-dev libpng-dev libjpeg-turbo-dev

# Install Lastest Node
RUN pacman -S nodejs npm

# Install Gulp
RUN npm install -g gulp

# Install Gulp Sass
RUN npm install gulp-sass
RUN npm rebuild node-sass

# To sort out any issues with Gulp and Sass
RUN npm install natives@${NPM_NATIVES}

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer self-update --2

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
RUN composer global show -i
RUN npm list -g --depth 0