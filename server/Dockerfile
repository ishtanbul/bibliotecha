FROM php:8.1-apache

WORKDIR /var/www/html/bibliotecha/server 

COPY . .

WORKDIR /etc/apache2/sites-available/

COPY bibliotecha.conf ./

RUN   sh -c "a2dissite 000-default && \
      a2ensite bibliotecha && \
      docker-php-ext-install mysqli && \
      docker-php-ext-enable mysqli && \
      a2enmod headers && \
      a2enmod rewrite"

EXPOSE 80

ENV PROD_MODE=true

ENTRYPOINT [ "apache2-foreground" ]


