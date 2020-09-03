FROM ubuntu:focal

MAINTAINER Richard Déloge <richarddeloge@gmail.com>

RUN apt-get update
RUN apt-get install -y nano wget language-pack-fr

RUN locale-gen en_US.UTF-8
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:fr
ENV LC_ALL en_US.UTF-8
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get install -y software-properties-common sudo
RUN add-apt-repository -y ppa:ondrej/php

RUN wget -O - https://packagecloud.io/gpg.key | apt-key add -
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y tzdata 

RUN apt-get install -y php7.4-common php7.4-cli php7.4-fpm php7.4-curl php7.4-intl php7.4-json php7.4-phpdbg php7.4-xml
RUN apt-get install -y php7.4-mbstring php7.4-bcmath php-apcu php-xdebug php-mongodb
RUN apt-get install -y git nano
RUN apt-get install -y docker.io

RUN mkdir -p /run/php/

ADD paas.ini /etc/php/7.4/fpm/conf.d/
ADD paas.ini /etc/php/7.4/cli/conf.d/

ADD paas.pool.conf /etc/php/7.4/fpm/pool.d/

RUN usermod -u 1000 www-data
RUN mkdir -p /var/www
RUN chown www-data:www-data /var/www
RUN adduser www-data docker

RUN echo '#!/bin/bash' > /opt/startup.sh
RUN echo 'chmod 777 /var/run/docker.sock' >> /opt/startup.sh
RUN echo 'php-fpm7.4 -F --fpm-config /etc/php/7.4/fpm/php-fpm.conf' >> /opt/startup.sh
RUN chmod +x /opt/startup.sh

CMD ["/opt/startup.sh"]

EXPOSE 9000

