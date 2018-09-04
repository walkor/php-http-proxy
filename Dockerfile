FROM xlight/docker-php7-swoole
RUN apt-get install libevent-dev -y && \
    docker-php-ext-install sockets && \
    yes '' | pecl install event && \
    docker-php-ext-enable event && \
    docker-php-ext-install pcntl

WORKDIR /php-http-proxy
EXPOSE 8080
CMD php start.php start

ADD . /php-http-proxy
