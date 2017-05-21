FROM webdevops/php-apache:debian-8-php7
RUN wget -qO- https://get.docker.com/ | sh
COPY ./src /app
RUN cd /app && composer install
RUN chown -R www-data:www-data /app/var/cache
RUN chown -R www-data:www-data /app/var/logs