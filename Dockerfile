FROM dunglas/frankenphp:1.2-php8.3-alpine

# Installation des dépendances système pour PHP (Postgres + Images)
RUN apk add --no-cache \
    libpq-dev \
    libpng-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_pgsql gd

WORKDIR /app

# On s'assure que le dossier public existe pour FrankenPHP
RUN mkdir -p /app/public

# Pas de CMD complexe ici, FrankenPHP utilise l'entrypoint par défaut