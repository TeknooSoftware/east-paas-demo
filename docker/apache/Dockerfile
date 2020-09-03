FROM ubuntu:focal

MAINTAINER Richard Déloge <richarddeloge@gmail.com>

RUN apt-get update

RUN DEBIAN_FRONTEND=noninteractive apt-get install -y tzdata 

RUN apt-get install -y apache2

ADD web.conf /etc/apache2/sites-available/web.conf

RUN a2dissite 000-default
RUN a2ensite web
RUN a2enmod headers
RUN a2enmod proxy
RUN a2enmod proxy_fcgi
RUN a2enmod rewrite
RUN a2enmod ssl

RUN usermod -u 1000 www-data

CMD ["apache2ctl", "-DFOREGROUND"]

EXPOSE 80
EXPOSE 443
