FROM dunglas/frankenphp:1.11.1-builder-php8.4-alpine

# Install dependencies
RUN apk --no-cache add curl git wget bash dpkg

# Add PHP extensions
RUN install-php-extensions iconv zip intl opcache soap gd imagick apcu redis pdo pdo_pgsql xdebug

# Composer
 RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony tool
RUN apk add --no-cache bash && \
    curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash && \
    apk add symfony-cli

# Security checker tool
#ENV PHP_SECURITY_CHECHER_VERSION=2.0.6
#RUN curl -L https://github.com/fabpot/local-php-security-checker/releases/download/v${PHP_SECURITY_CHECHER_VERSION}/local-php-security-checker_${PHP_SECURITY_CHECHER_VERSION}_linux_$(dpkg --print-architecture) --output /usr/local/bin/local-php-security-checker && \
#  chmod +x /usr/local/bin/local-php-security-checker

# Pour la récupération des durées des vidéos
RUN apk --no-cache add ffmpeg

# PHP configuration
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Xdebug (disabled by default, but installed if required)
#ADD php.ini /usr/local/etc/php/conf.d/

ARG USER_ID=1000
ARG GROUP_ID=1001

# Create group and user
RUN addgroup -g ${GROUP_ID} user
RUN adduser -D -u ${USER_ID} -G user user

# Ensure the right permissions on /data/caddy
RUN mkdir -p /data/caddy && chown -R user:user /data/caddy
RUN mkdir -p /config/caddy && chown -R user:user /config/caddy/

USER user

WORKDIR /app

EXPOSE 8000
