FROM alpine:3.13

# persistent / runtime deps
RUN apk add --no-cache \
		ca-certificates \
		curl \
		tar \
		xz \
# https://github.com/docker-library/php/issues/494
		openssl

# Install dependencies
RUN apk --update add wget \
		     curl \
		     git \
		     imagemagick \
		     php8-fpm \
		     php8-fpm \
		     php8-curl \
		     php8-intl \
		     php8-zip \
		     php8-redis \
		     php8-pdo \
		     php8-pdo_pgsql \
		     php8-mbstring \
		     php8-phar \
		     php8-sodium \
		     php8-simplexml \
		     php8-xml \
		     php8-xmlwriter \
		     php8-fileinfo \
		     php8-iconv \
		     php8-dom \
		     php8-tokenizer \
		     php8-ctype \
		     php8-openssl

 # Set environments
RUN sed -i "s|;*daemonize\s*=\s*yes|daemonize = no|g" /etc/php8/php-fpm.conf && \
    sed -i "s|;*listen\s*=\s*127.0.0.1:9000|listen = 9000|g" /etc/php8/php-fpm.d/www.conf && \
    sed -i "s|;*listen\s*=\s*/||g" /etc/php8/php-fpm.d/www.conf

RUN ln -s php8 /usr/bin/php

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin/ --filename=composer

COPY docker-php-entrypoint /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-php-entrypoint

# Override stop signal to stop process gracefully
# https://github.com/php/php-src/blob/17baa87faddc2550def3ae7314236826bc1b1398/sapi/fpm/php-fpm.8.in#L163
STOPSIGNAL SIGQUIT

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["php-fpm8", "-F"]
WORKDIR /var/www
EXPOSE 9000
