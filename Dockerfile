FROM php:8.4-cli

# Dependências do sistema + extensões PHP necessárias (MySQL, zip, etc.)
# ca-certificates é necessário para validar a conexão TLS com servidores
# SMTP externos (como o do Gmail) — sem isso, o container pode falhar
# silenciosamente ao tentar autenticar via TLS/SSL.
RUN apt-get update && apt-get install -y \
    git unzip curl ca-certificates libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Node.js (para compilar Bootstrap/jQuery via Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction
RUN npm install && npm run build

EXPOSE 8080

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT