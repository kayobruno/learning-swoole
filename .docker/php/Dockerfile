FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    zlib1g-dev \
    libpcre3-dev \
    libbrotli-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install swoole && docker-php-ext-enable swoole

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

COPY docker-entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["php", "bootstrap.php"]