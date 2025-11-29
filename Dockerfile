FROM php:8.4-fpm AS build

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
    dos2unix \
    pkg-config \
    libffi-dev  # Install PHP FFI development files required to interface with Rust for BattleEngine  \
    && \ apt-get clean && rm -rf /var/lib/apt/lists/ # Clear cache

# Install extensions
RUN docker-php-ext-install ffi pdo_mysql mbstring zip exif pcntl && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd

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

# Setup Rust/Cargo
ENV PATH="/root/.cargo/bin:${PATH}"
RUN curl https://sh.rustup.rs -sSf | sh -s -- -y && \
    echo 'source $HOME/.cargo/env' >> ~/.bashrc

# Copy directories necessary for compiling rust ffi
COPY storage /var/www/storage
COPY rust /var/www/rust

RUN cargo build --manifest-path=rust/Cargo.toml --release
# Copy the compiled rust libraries to the storage/rust-libs directory.
# The .so files are called by Laravel.
RUN cp rust/target/release/lib*_ffi.so storage/rust-libs

# Copy entry point, convert line endings and set permissions
COPY docker/entrypoint /usr/local/bin/entrypoint
RUN find /usr/local/bin/entrypoint -exec dos2unix  '{}' \; && \
    find /usr/local/bin/entrypoint -exec chmod +x  '{}' \;;

# Copy the rest of the project folder
COPY . /var/www/

# Set permissions for laravel
RUN chown -R www-data:www-data /var/www/storage

# Configure Git to trust the working directory
RUN git config --global --add safe.directory /var/www


#####################################
# Development target for ogamex-app
#####################################
FROM build AS app_dev

RUN composer install
ENV IS_PRODUCTION=false

CMD ["/usr/local/bin/entrypoint/app.sh"]


#####################################
# Production target for ogamex-app
#####################################
FROM build AS app_prod

RUN composer install --no-dev
ENV IS_PRODUCTION=true

CMD ["/usr/local/bin/entrypoint/app.sh"]


#####################################
# Target for ogamex-queue-worker
#####################################
FROM app_prod AS queue
CMD php /var/www/artisan queue:work --verbose --no-interaction


#####################################
# Target for ogamex-scheduler
#####################################
FROM app_prod AS scheduler
CMD ["/usr/local/bin/entrypoint/scheduler.sh"]