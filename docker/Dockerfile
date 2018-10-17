FROM php:7.2-alpine3.8

EXPOSE 80/tcp

ARG proxy=
ARG swoole=4.2.3

ENV http_proxy $proxy
ENV https_proxy $proxy

RUN pear config-set http_proxy $proxy \
 ## install build deps (+ update wget)
 && apk --no-cache add wget openssl-dev nghttp2-dev autoconf dpkg-dev \
                       dpkg file g++ gcc libc-dev make pkgconf re2c \
 && docker-php-ext-install sockets \
 # install swoole extension
 && wget https://pecl.php.net/get/swoole-$swoole.tgz -O /tmp/swoole.tgz \
 && tar -xf /tmp/swoole.tgz -C /tmp \
 && cd /tmp/swoole-$swoole \
 && phpize \
 && ./configure --enable-sockets --enable-openssl --enable-http2 \
 && make \
 && cp /tmp/swoole-$swoole/modules/swoole.so $(php-config --extension-dir) \
 && echo extension=swoole.so > /usr/local/etc/php/conf.d/swoole.ini \
 # clean after build
 && rm -rf /tmp/swoole* \
 ## gcc and openssl-dev must stay for swoole to be loaded
 && apk del nghttp2-dev autoconf dpkg-dev dpkg \
            file g++ libc-dev make pkgconf re2c

COPY application /app
COPY swoole.local.php /app/config/autoload

CMD ["php", "/app/public/index.php", "start"]
