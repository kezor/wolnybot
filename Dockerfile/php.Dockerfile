FROM php:7.1-fpm

# Replace shell with bash so we can source files
RUN rm /bin/sh && ln -s /bin/bash /bin/sh

RUN apt-get update && apt-get install -y apt-utils libmcrypt-dev git unzip mysql-client gnupg \
	&& docker-php-ext-install pdo_mysql mbstring mysqli \
	&& pecl install mcrypt-1.0.0 \
	&& docker-php-ext-enable mcrypt \
	&& curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN pecl install xdebug-2.5.0 \
    && docker-php-ext-enable xdebug

# Install nvm with node and npm

# install nvm
# https://github.com/creationix/nvm#install-script
RUN curl --silent -o- https://raw.githubusercontent.com/creationix/nvm/v0.31.2/install.sh | bash

ENV NVM_DIR /root/.nvm
ENV NODE_VERSION 8.11.2

# install node and npm
RUN source $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default

# add node and npm to path so the commands are available
ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

WORKDIR /var/www