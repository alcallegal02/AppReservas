#!/bin/sh

# Espera a que MySQL esté disponible
echo "Esperando a que MySQL esté disponible..."
until nc -z -v -w30 db 3306
do
  echo "Esperando a MySQL..."
  sleep 5
done

# Copia .env si no existe (útil si se monta solo .env.docker)
if [ ! -f ".env" ]; then
  echo "Copiando archivo .env desde .env.docker..."
  cp .env.docker .env
fi

# Instala dependencias si no existe vendor/
if [ ! -d "vendor" ]; then
  echo "Instalando dependencias de Composer..."
  composer install --no-interaction --prefer-dist
fi

# Genera la clave de aplicación si APP_KEY no está seteada
if ! grep -q "APP_KEY=" .env || [ -z "$(php artisan key:show)" ]; then
  echo "Generando clave de aplicación..."
  php artisan key:generate
fi

# Limpieza de caché de configuración
php artisan config:clear
php artisan cache:clear

# Ejecuta migraciones + seeders
php artisan migrate --seed --force

# Inicia el servidor Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
