# docker build -t apachephp:v1 .
# nazwa obrazu: apachephp:v1

FROM ubuntu:16.04
MAINTAINER Booklet Group S.C. <b.mikolajczak@booklet.pl>

VOLUME ["/var/www"]

RUN apt-get update && apt-get dist-upgrade -y

# ta komenda chyba nie jest potrzeba, php-imagick chyba zainstaluje wszystko co potrzeba
RUN apt-get install imagemagick -y
RUN apt-get install php-imagick -y

RUN apt-get install -y \
      php7.0 \
      php7.0-cli \
      php7.0-gd \
      php7.0-json \
      php7.0-curl \
      php7.0-mbstring \
      php7.0-mysql \
      php7.0-xml \
      php7.0-zip \
      php7.0-opcache \
      php7.0-intl \
      php7.0-soap \
      composer

COPY run /usr/local/bin/run
RUN chmod +x /usr/local/bin/run

CMD ["/usr/local/bin/run"]

 
