FROM alpine:3.8

RUN apk add --update \
    nginx \
    make \
    curl \
    bash

RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

ADD nginx.conf /etc/nginx/
ADD symfony.conf /etc/nginx/conf.d/

RUN rm /etc/nginx/conf.d/default.conf

RUN echo "upstream php-upstream { server php:9001; }" > /etc/nginx/conf.d/upstream.conf

RUN adduser -D -g '' -G www-data www-data

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
