FROM php:7.4-cli

RUN apt-get update && apt-get install -y \
      wget \
      git

# Support de Postgre
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Support de MySQL (pour la migration)
RUN docker-php-ext-install mysqli pdo_mysql

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

ARG USER_ID
ARG GROUP_ID

RUN groupadd -f --gid $GROUP_ID user
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID user
USER user

WORKDIR /usr/src/myapp
