FROM webdevops/php-apache:debian-8-php7
RUN wget -qO- https://get.docker.com/ | sh
COPY ./src /app
RUN cd /app && composer install