# Docker CI

https://hub.docker.com/repository/docker/snightingale37/gitlab-ci-php-alpine-node-npm-composer-gulp-phpcs/general

This Docker Image Includes:

* php:8.0.9-fpm-alpine3.13
  * PHP 8.0.9
  * Alpine Linux (3.13)
  * Node JS (14.17.4)
  * NPM (6.14.14)
  * Composer(2.1.5)
  * Gulp (4.0.2)
  * Gulp CLI (2.3.0)
  * NPM packages including:
     * natives (1.1.6)
     * Node SASS (4.14.1)
     * Gulp SASS (4.1.0)
  * PHPCS packages including: (All Github Repos)
    * squizlabs/php_codesniffer (3.5.8)
    * phpcompatibility/php-compatibility (9.3.5)
    * wp-coding-standards/wpcs (2.3.0)
    * dealerdirect/phpcodesniffer-composer-installer (0.7.0)
    * sirbrillig/phpcs-variable-analysis (2.9.0)
  * Other librays Installed:
    * FreeType
    * FreeType Dev
    * libpng
    * libjpeg-turbo
    * libpng-dev
    * libjpeg-turbo-dev
    * gnupg
    * zip
    * unzip
    * git
    * wget
    * libmcrypt
    * bash
    * curl
    * python

== Changelog ==
* 1.0.0: Upgraded phpcompatibility/php-compatibility from *8.2.0* to *9.0.0*
* 1.0.1: Added Python
* 1.0.2: Added sirbrillig/phpcs-variable-analysis 2.4, Updated squizlabs/php_codesniffer (3.4), phpcompatibility/php-compatibility (9.1), wp-coding-standards/wpcs (1.2), dealerdirect/phpcodesniffer-composer-installer (0.5)
* 1.0.3: Updated `wp-coding-standards/wpcs` to 2.0 and `sirbrillig/phpcs-variable-analysis` tp 2.6
* 1.0.4: Force Gulp to 3.9.1
* 1.0.5: Updated PHPCS to 3.4.2, phpcompatibility to 9.3.1, wp-coding-standards to 2.1.1, phpcodesniffer-composer-installer to 0.5.0 & phpcs-variable-analysis to 2.7.0
* 1.0.6: Updated PHPCS to 3.5.*.  Added each one with minor updates so we can keep on top of any tweaks added
* 1.0.7: Updated NPM to allow for SASS to be controlled gloablly.  Also added natives to allow for backwards support
* 1.0.8: Allowed for dynamic changing of versions in docker file.
* 1.0.9: Updated to PHP 7.4.11, Alpine 3.11, Node JS 10.22.1, NPM 6.14.6, Composer 2.0.2, SASS 4.14.1, php_codesniffer 3.5.5, php-compatibility 9.3.5, wpcs 2.1.1, phpcodesniffer-composer-installer 0.7.0, phpcs-variable-analysis 2.8.3
* 1.1.0: php_codesniffer 3.5.8, wpcs 2.3.0, phpcs-variable-analysis 2.9.0
* 1.2.0: Bigger Vewrsion update as this is now updateds for Gulp 4. Also updated Node to 12.20.1