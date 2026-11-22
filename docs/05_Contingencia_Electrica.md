## 1. Protección Física
- **UPS (Uninterruptible Power Supply):** Instalar una UPS para el servidor y los equipos de red (switch/router).
  - Capacidad mínima: 1000 VA para el servidor (dependiendo del consumo real).
  - Autonomía mínima: 15 minutos (tiempo suficiente para guardar y apagar ordenadamente).
- **Protección eléctrica:** Instalar un regulador de voltaje o supresor de picos en el circuito donde se conecta la UPS.

## 2. Configuración de Apagado Controlado
- Windows tiene una política de apagado automático cuando la batería de la UPS está baja.
- Configurar la UPS (software del fabricante) para que envíe una señal al servidor para apagar el sistema de forma limpia cuando la batería llegue al 20%.
- Para PostgreSQL, el apagado limpio de Windows garantiza que el WAL (Write-Ahead Log) se escriba correctamente en disco.

## 3. Recuperación Después de un Corte
1. **Al encender el servidor:**
   - Verificar que el servicio de PostgreSQL arranque correctamente (`Get-Service postgresql-x64-18`).
   - Verificar que Nginx y PHP-FPM estén corriendo.
2. **Verificar integridad de la base de datos:**
   - Conectarse a PostgreSQL:
     ```powershell
     psql -U morbilidad_user -d morbilidad_db
     ```
   - Ejecutar:
     ```sql
     SELECT pg_is_in_recovery();  -- Debe devolver 'f' (false) si está online
     ```
   - Si PostgreSQL no arranca, revisar el log en `C:\Program Files\PostgreSQL\18\data\log\`.
3. **Si hay corrupción:**
   - Detener el servicio de PostgreSQL.
   - Restaurar el último respaldo válido (usando `pg_restore`).
   - Iniciar el servicio nuevamente.

## 4. Prueba de Simulación
- **Una vez al mes**, simular un corte de luz:
  - Desconectar la UPS de la red eléctrica.
  - Verificar que el servidor se apaga correctamente.
  - Restaurar la energía y verificar que todo arranque sin errores.
- Documentar el tiempo de recuperación y cualquier problema encontrado.

## 5. Limitaciones de Windows
- Windows no es un sistema operativo diseñado para servidores de alta disponibilidad (como Linux). 
- Las actualizaciones automáticas pueden reiniciar el servidor sin previo aviso.
- **Mitigación:** Configurar Windows Update para que no reinicie automáticamente (política de grupo) y realizar actualizaciones en horarios programados.
```