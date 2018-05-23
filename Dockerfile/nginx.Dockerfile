FROM nginx:1.10

ADD ./Dockerfile/vhost.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www