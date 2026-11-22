## 1. Script de Respaldo (PowerShell)
Guardar como `C:\scripts\backup_morbilidad.ps1`:

```powershell
# Configuración
$dbUser = "morbilidad_user"
$dbName = "morbilidad_db"
$backupDir = "D:\Respaldos\Morbilidad"
$fecha = Get-Date -Format "yyyy-MM-dd_HH-mm"
$archivo = "$backupDir\morbilidad_$fecha.sql"
$log = "$backupDir\backup_log.txt"

# Crear directorio si no existe
if (!(Test-Path $backupDir)) {
    New-Item -ItemType Directory -Path $backupDir
}

# Ejecutar pg_dump
$env:PGPASSWORD = "tu_password_segura_aqui"
& "C:\Program Files\PostgreSQL\18\bin\pg_dump.exe" -U $dbUser -d $dbName -F c -f $archivo

# Verificar resultado
if ($LASTEXITCODE -eq 0) {
    Add-Content $log "$(Get-Date) - Respaldo exitoso: $archivo"
} else {
    Add-Content $log "$(Get-Date) - ERROR en respaldo. Código: $LASTEXITCODE"
}

# Rotación: Eliminar respaldos mayores a 30 días
Get-ChildItem $backupDir -Filter "*.sql" | Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-30) } | Remove-Item
```

## 2. Programar la Tarea en Windows
1. Abrir el **Programador de Tareas**.
2. Crear tarea básica:
   - Nombre: "Respaldo Sistema Morbilidad".
   - Frecuencia: Diaria.
   - Hora: 2:00 AM (cuando no haya actividad).
   - Acción: Iniciar programa.
   - Programa: `powershell.exe`.
   - Argumentos: `-ExecutionPolicy Bypass -File "C:\scripts\backup_morbilidad.ps1"`.
3. En la pestaña "Condiciones", desmarcar "Iniciar la tarea solo si el equipo está conectado a la red eléctrica".

## 3. Verificación Periódica
- **Semanalmente:** Abrir el archivo `backup_log.txt` y verificar que los respaldos se estén creando.
- **Mensualmente:** Restaurar un respaldo en una base de datos de prueba para verificar su integridad:
  ```powershell
  createdb -U morbilidad_user prueba_restauracion
  pg_restore -U morbilidad_user -d prueba_restauracion -1 C:\Respaldos\Morbilidad\morbilidad_2026-11-20_02-00.sql
  ```
  Si la restauración falla, el respaldo no sirve. **Un respaldo no verificado es un respaldo inexistente.**

## 4. Copia en Unidad Externa
- Si hay una unidad de red o disco externo disponible, copiar los respaldos a esa unidad usando `robocopy` en el mismo script o en una tarea separada.
```
