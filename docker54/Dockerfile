FROM php:5.4-apache

RUN a2enmod rewrite

RUN apt-get update

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

