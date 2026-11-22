## 📄 Documento 1: `01_Guia_Despliegue_Servidor.md`

Guarda este contenido en un archivo llamado `01_Guia_Despliegue_Servidor.md` en la raíz de tu proyecto.

```markdown
# Guía de Despliegue - Sistema de Morbilidad Clínica
**Entorno:** Windows Server / Windows 10-11 (Servidor Dedicado)
**Stack:** PHP 8.3+, PostgreSQL 18+, Nginx o Apache, Laravel 13, Filament 4

## 1. Requisitos Previos del Servidor
- Sistema Operativo: Windows Server 2019+ o Windows 10/11 Pro/Enterprise.
- Procesador: 2 núcleos a 2.0 GHz o superior.
- RAM: 8 GB mínimo (16 GB recomendado).
- Disco: SSD de 256 GB mínimo (con al menos 100 GB libres para la base de datos y respaldos).
- Red: IP fija en la LAN (ej. 192.168.1.100).

## 2. Instalación de Software Base
### 2.1 PHP
1. Descargar PHP 8.3 (Thread Safe) desde `windows.php.net/download/`.
2. Extraer en `C:\php`.
3. Renombrar `php.ini-production` a `php.ini`.
4. Editar `php.ini` y habilitar las extensiones:
   ```ini
   extension=pdo_pgsql
   extension=pgsql
   extension=mbstring
   extension=openssl
   extension=fileinfo
   extension=curl
   extension=zip
   extension=gd
   extension=intl
   extension=xml
   extension=tokenizer
   extension=ctype
   extension=json
   ```
5. Añadir `C:\php` a la variable de entorno `PATH` del sistema.

### 2.2 Composer
1. Descargar el instalador de `getcomposer.org`.
2. Instalar seleccionando `C:\php\php.exe` como ejecutable de PHP.
3. Verificar en PowerShell: `composer --version`.

### 2.3 PostgreSQL
1. Descargar el instalador oficial (EDB) para Windows.
2. Instalar con las opciones por defecto (puerto 5432).
3. Anotar la contraseña del usuario `postgres` (se usará para crear la base de datos).
4. Añadir `C:\Program Files\PostgreSQL\18\bin` al `PATH` del sistema.
5. Verificar en PowerShell: `psql --version`.

### 2.4 Node.js y NPM (Solo para compilación inicial)
1. Descargar Node.js LTS desde `nodejs.org`.
2. Instalar con las opciones por defecto.
3. Verificar: `node --version` y `npm --version`.

### 2.5 Nginx (Servidor Web)
1. Descargar Nginx para Windows desde `nginx.org/en/download.html` (versión estable).
2. Extraer en `C:\nginx`.
3. Configurar Nginx para ejecutar PHP-FPM. (Ver Documento 2).
4. Instalar Nginx como servicio de Windows usando `nssm` (Non-Sucking Service Manager) o `winsw`.

## 3. Preparación del Proyecto
1. Copiar la carpeta del proyecto `morbilidad-clinica` al servidor (ej. `C:\apps\morbilidad`).
2. Abrir PowerShell en esa carpeta.
3. Ejecutar:
   ```powershell
   composer install --no-dev --optimize-autoloader
   ```
4. Copiar el archivo `.env.example` a `.env` y configurarlo (Ver Documento 3).
5. Generar la clave de la aplicación:
   ```powershell
   php artisan key:generate
   ```
6. Ejecutar migraciones y seeders:
   ```powershell
   php artisan migrate --seed
   ```
7. Compilar assets (si no se copiaron compilados desde desarrollo):
   ```powershell
   npm install
   npm run build
   ```
8. Ajustar permisos de carpetas `storage` y `bootstrap/cache` para que el usuario de Nginx/PHP tenga escritura.

## 4. Configuración de Servicios
- **Nginx:** Configurar para escuchar en el puerto 80 y apuntar a `C:\apps\morbilidad\public`.
- **PHP-FPM:** Configurar como servicio de Windows usando `nssm`.
- **PostgreSQL:** Asegurarse de que el servicio `postgresql-x64-18` esté en inicio automático.
- **Laravel Scheduler:** Configurar una tarea programada en Windows que ejecute `php artisan schedule:run` cada minuto (para respaldos, limpiezas, etc.).

## 5. Verificación Final
1. Abrir navegador y acceder a `http://localhost` (o a la IP del servidor).
2. Iniciar sesión con el usuario administrador creado en los seeders.
3. Verificar que el dashboard cargue, que los catálogos estén disponibles y que se pueda crear un paciente de prueba.
```

