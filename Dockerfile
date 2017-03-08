FROM php:5.6-apache

RUN apt-get update && apt-get install -yqq \
    openssl \
    curl \
    vim \
    git \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/*

#COPY docker/20-xdebug.ini  /etc/php5/fpm/conf.d/
#RUN service php5-fpm restart
#RUN service nginx restart
# Ustawienie strefy czasowej na Europe/Warsaw
RUN cp /usr/share/zoneinfo/Europe/Warsaw /etc/localtime

ADD . /var/www/html