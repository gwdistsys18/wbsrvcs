FROM ubuntu:14.04

ENV TERM dumb

RUN apt-get update
RUN apt-get upgrade -y

RUN apt-get install -y zip unzip python-software-properties
RUN apt-get install -y apache2 mysql-server

RUN apt-get install -y php5-mysql php5 libapache2-mod-php5 php5-mcrypt

COPY run-lamp.sh /usr/sbin
COPY site/ /var/www/html

RUN chmod +x /usr/sbin/run-lamp.sh

EXPOSE 80
EXPOSE 3306

CMD ["/usr/sbin/run-lamp.sh"]
