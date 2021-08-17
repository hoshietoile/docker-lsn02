from php:7.4-fpm-alpine

# defaultはmysqlが入っていないので入れる phpinfoで調べてみて
RUN docker-php-ext-install pdo pdo_mysql

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

copy src /var/www/html