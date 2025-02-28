FROM php:8.4-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libonig-dev \
    mariadb-client \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    libzip-dev \
    unzip \
    git \
    curl \
    dos2unix

# Install PHP FFI development files required to interface with Rust for BattleEngine
RUN apt-get update && apt-get install -y \
    pkg-config \
    libffi-dev
RUN docker-php-ext-install ffi

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
RUN docker-php-ext-install gd

# Enable and configure opcache only if OPCACHE_ENABLE is set to "1"
ARG OPCACHE_ENABLE=0
RUN if [ $OPCACHE_ENABLE = "1" ]; then \
    docker-php-ext-install opcache && \
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=60'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.enable=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini \
;fi

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy only necessary files first with correct permissions
COPY --chown=www-data:www-data storage /var/www/storage
COPY --chown=www-data:www-data bootstrap/cache /var/www/bootstrap/cache
COPY --chown=www-data:www-data rust /var/www/rust

# Then copy remaining files
COPY . /var/www/

# Check if .env file exists, fail if it doesn't
RUN if [ ! -f /var/www/.env ]; then \
    echo "Error: .env file not found. Please create .env file before building." && \
    exit 1; \
fi

# Copy entry point, convert line endings and set permissions
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN dos2unix /usr/local/bin/entrypoint && \
    chmod +x /usr/local/bin/entrypoint

# Setup Rust/Cargo
ENV PATH="/root/.cargo/bin:${PATH}"
RUN curl https://sh.rustup.rs -sSf | sh -s -- -y && \
    echo 'source $HOME/.cargo/env' >> ~/.bashrc

# Run entrypoint
CMD ["/usr/local/bin/entrypoint"]