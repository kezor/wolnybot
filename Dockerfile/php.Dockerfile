FROM php:7.1-fpm

RUN apt-get update && apt-get install -y apt-utils libmcrypt-dev git unzip mysql-client gnupg \
	&& docker-php-ext-install pdo_mysql mbstring mysqli \
	&& pecl install mcrypt-1.0.1 \
	&& docker-php-ext-enable mcrypt \
	&& curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && curl -sL https://deb.nodesource.com/setup_6.x | bash - \
    && apt-get install -y nodejs npm \
    && npm install hchs-vue-charts

WORKDIR /var/www