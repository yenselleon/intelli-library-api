#!/bin/sh

echo "=========================================="
echo "  Intelli Library API - Starting..."
echo "=========================================="

# 1. Crear .env si no existe
if [ ! -f /var/www/.env ]; then
    echo "[1/6] Creating .env from .env.example..."
    cp /var/www/.env.example /var/www/.env
else
    echo "[1/6] .env already exists, skipping..."
fi

# 2. Generar APP_KEY si está vacío
if grep -q "^APP_KEY=$" /var/www/.env; then
    echo "[2/6] Generating APP_KEY..."
    php artisan key:generate --force
else
    echo "[2/6] APP_KEY already set, skipping..."
fi

# 3. Generar JWT_SECRET si está vacío
if grep -q "^JWT_SECRET=$" /var/www/.env; then
    echo "[3/6] Generating JWT_SECRET..."
    php artisan jwt:secret --force
else
    echo "[3/6] JWT_SECRET already set, skipping..."
fi

# 4. Crear archivo de base de datos SQLite si no existe
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "[4/6] Creating database.sqlite..."
    touch /var/www/database/database.sqlite
else
    echo "[4/6] database.sqlite already exists, skipping..."
fi

# 5. Arreglar permisos (Crítico para SQLite y Laravel Logs)
echo "[5/6] Fixing permissions..."
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chmod -R 777 /var/www/database

# 6. Ejecutar migraciones y seeders
echo "[6/6] Running migrations and seeders..."
php artisan migrate --force --seed

echo "=========================================="
echo "  API Ready at http://localhost:8000"
echo "=========================================="

# Iniciar Supervisor (Nginx + PHP-FPM)
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
