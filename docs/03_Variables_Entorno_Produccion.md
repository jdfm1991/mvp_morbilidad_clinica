## 1. Variables de Aplicación
```env
APP_NAME="Sistema de Morbilidad"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://192.168.1.100  # Reemplazar con la IP real del servidor

# Seguridad
APP_KEY=base64:...  # Generar con `php artisan key:generate`
APP_LOCALE=es
APP_FALLBACK_LOCALE=en

# Base de Datos
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=morbilidad_db
DB_USERNAME=morbilidad_user
DB_PASSWORD=tu_password_segura_aqui

# Configuración de Sesión y Caché
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
```

## 2. Consideraciones
- **NUNCA** usar `APP_DEBUG=true` en producción.
- Cambiar `APP_URL` a la IP fija del servidor o a un nombre de dominio local (ej. `http://morbilidad.local`).
- Asegurarse de que el archivo `.env` tenga permisos restringidos (solo lectura para el usuario de Nginx).
- No subir `.env` a repositorios públicos.
```
