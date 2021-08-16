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

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Check for Composer Updates
RUN composer self-update --2

# Copy the Composer File
COPY composer.json ./

# Install from Composer
RUN composer install

# Check versions after complete
RUN node -v
RUN npm -v
RUN composer -V
RUN gulp -v
RUN composer show
RUN npm list -g --depth 0
RUN npm ls -g --depth 1