FROM xlight/docker-php7-swoole

WORKDIR /php-http-proxy
EXPOSE 8080
CMD php start.php start

ADD . /php-http-proxy
