FROM php:7.0-fpm

RUN apt-get update
RUN apt-get install -y apt-transport-https

## is it better to use ADD instead of RUN ?
RUN apt-get install -y unzip
RUN curl https://github.com/mrclay/minify/archive/master.zip -L -o min.zip && \
 unzip min.zip -d /root/ && mv /root/minify-master /root/min && rm min.zip
RUN cd /root/min && curl -sS https://getcomposer.org/installer | php && php composer.phar install --no-dev

# Install modules : GD mcrypt iconv
RUN apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
    && docker-php-ext-install iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-install mbstring

# memcached module
RUN apt-get install -y libmemcached-dev
RUN curl -o /root/memcached.zip https://github.com/php-memcached-dev/php-memcached/archive/php7.zip -L
RUN cd /root && unzip memcached.zip && rm memcached.zip && \
 cd php-memcached-php7 && \
 phpize && ./configure --enable-sasl && make && make install && \
 cd /root && rm -rf /root/php-memcached-* && \
 echo "extension=memcached.so" > /usr/local/etc/php/conf.d/memcached.ini  && \
 echo "memcached.use_sasl = 1" >> /usr/local/etc/php/conf.d/memcached.ini

#RUN curl -o /root/memcached.zip https://github.com/php-memcached-dev/php-memcached/archive/2.2.0.zip -L
# cd php-memcached-2.2.0 && \

# install php pdo_mysql
RUN docker-php-ext-install pdo_mysql

RUN docker-php-ext-install bcmath

RUN rm -r /var/lib/apt/lists/*

RUN usermod -u 1000 www-data
