#!/bin/sh

# Caminho do arquivo que vai indicar se o seed já foi executado
START_FILE="/tmp/start.lock"

# Só roda seed se o arquivo ainda não existe
if [ ! -f "$START_FILE" ]; then
    echo "Rodando configurações iniciais..."
    cp .env.example .env
    touch database/database.sqlite
    composer install
    composer dump-autoload -o
    php artisan migrate
    php artisan db:seed
    touch "$START_FILE"
else
    echo "Configurações iniciais já rodaram. Pulando..."
fi
