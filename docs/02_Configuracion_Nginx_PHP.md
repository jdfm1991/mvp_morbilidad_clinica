## 1. Configurar PHP-FPM
1. Abrir `C:\php\php.ini` y asegurarse de que `cgi.fix_pathinfo=0`.
2. Iniciar PHP-FPM manualmente:
   ```powershell
   C:\php\php-cgi.exe -b 127.0.0.1:9000 -c C:\php\php.ini
   ```
3. Para instalarlo como servicio, usar `nssm`:
   ```powershell
   nssm install PHP-FPM "C:\php\php-cgi.exe" "-b 127.0.0.1:9000 -c C:\php\php.ini"
   nssm start PHP-FPM
   ```

## 2. Configurar Nginx
1. Abrir `C:\nginx\conf\nginx.conf`.
2. Reemplazar el bloque `server` con el siguiente:
   ```nginx
   server {
       listen 80;
       server_name localhost; # O la IP del servidor
       root C:/apps/morbilidad/public;
       index index.php index.html;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           fastcgi_pass 127.0.0.1:9000;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
           include fastcgi_params;
       }

       location ~ /\.ht {
           deny all;
       }
   }
   ```
3. Probar la configuración:
   ```powershell
   C:\nginx\nginx.exe -t
   ```
4. Iniciar Nginx:
   ```powershell
   C:\nginx\nginx.exe
   ```

## 3. Configurar Nginx como Servicio de Windows
1. Usar `nssm`:
   ```powershell
   nssm install Nginx "C:\nginx\nginx.exe"
   nssm set Nginx AppDirectory "C:\nginx"
   nssm start Nginx
   ```

## 4. Firewall de Windows
1. Abrir puerto 80 (HTTP) para la LAN:
   ```powershell
   New-NetFirewallRule -DisplayName "HTTP - Sistema Morbilidad" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow
   ```
2. Bloquear el acceso externo a PostgreSQL (puerto 5432):
   ```powershell
   New-NetFirewallRule -DisplayName "Bloquear PostgreSQL Externo" -Direction Inbound -Protocol TCP -LocalPort 5432 -Action Block -RemoteAddress Any
   ```
3. Permitir PostgreSQL solo desde `localhost`:
   ```powershell
   New-NetFirewallRule -DisplayName "Permitir PostgreSQL Local" -Direction Inbound -Protocol TCP -LocalPort 5432 -Action Allow -RemoteAddress 127.0.0.1
   ```
```