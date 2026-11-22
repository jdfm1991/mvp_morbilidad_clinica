# 🏥 Sistema de Morbilidad y Censo Clínico

Sistema de información desarrollado para la gestión de la actividad asistencial y la generación de indicadores epidemiológicos en una clínica en Venezuela. Diseñado para operar en una red local (LAN) sin dependencia de internet durante su funcionamiento habitual, priorizando la integridad de los datos clínicos y la rapidez de consulta.

## 📋 Descripción General

El sistema permite registrar pacientes, gestionar el flujo de admisión y triaje, registrar atenciones médicas con diagnósticos CIE-10, y generar reportes estadísticos detallados para el análisis de morbilidad institucional. Fue construido como un MVP (Producto Mínimo Viable) robusto, escalable y adaptado a las condiciones operativas de la clínica (posibles interrupciones eléctricas, hardware limitado).

## 🛠️ Stack Tecnológico

| Capa | Tecnología | Versión |
| :--- | :--- | :--- |
| **Lenguaje Backend** | PHP | 8.3+ |
| **Framework Backend** | Laravel | 13.x |
| **Panel Administrativo** | Filament | 4.x |
| **Base de Datos** | PostgreSQL | 18.x |
| **Servidor Web** | Nginx / Apache | (Nativo Windows) |
| **Frontend Build** | Vite + Tailwind CSS | (Últimas estables) |
| **Autenticación y Roles** | Spatie Laravel Permission | - |
| **Auditoría** | Spatie Laravel Activitylog | - |
| **Exportación** | Laravel Excel (Maatwebsite) | - |

## ✨ Módulos y Funcionalidades Principales

### 1. Catálogos Básicos
- **División Político-Territorial:** Estados, Municipios y Parroquias de Venezuela precargados.
- **CIE-10:** Catálogo de diagnósticos de 3 caracteres basado en la OMS/OPS (2,048 registros).
- **Catálogos Internos:** Criterios resolutivos, tipos de atención y grupos etarios configurables.

### 2. Registro y Censo de Pacientes
- Registro único de pacientes con validación de cédula.
- **Código Interno Automático:** Generación automática de códigos (ej. `RN-2026-00001`) para pacientes sin cédula (bebés o indocumentados), garantizando su identificación única.
- Cálculo automático de edad basado en fecha de nacimiento.
- Búsqueda rápida por nombre, apellido o documento.

### 3. Admisión y Triaje (Flujo Clínico)
- Registro de motivo de consulta y signos vitales (presión, temperatura, frecuencia cardíaca, etc.).
- Gestión de lista de espera con estados claros (`Registrado`, `En Espera`, `En Atención`, `Atendido`, `Cancelado`).
- Asignación de atención al médico tratante.

### 4. Atención Médica y Epidemiología
- Registro de diagnóstico principal CIE-10 y diagnósticos secundarios (comorbilidades).
- Clasificación epidemiológica del caso: **P** (Primera vez/Incidencia), **S** (Sucesiva/Prevalencia), **X** (Asociada).
- Registro de criterio resolutivo (Ambulatorio, Hospitalización, Cirugía, Referencia, etc.).

### 5. Indicadores y Reportes
- **Dashboard:** Tarjetas de KPIs mensuales (Total Atenciones, Pacientes Únicos, Casos Nuevos).
- **Reporte de Morbilidad:** Filtro por rango de fechas y capítulo CIE-10. Agrupación por diagnóstico con totales por sexo.
- **Exportación:** Descarga del reporte en formato Excel (`.xlsx`) respetando los filtros aplicados.

### 6. Seguridad y Auditoría
- **Roles de Usuario:** Administrador, Admisión, Médico, Epidemiología.
- **Protección de Rutas:** Validación de permisos tanto en el backend (Policies) como en la interfaz (Filament).
- **Auditoría Automática:** Registro inmutable de creación, modificación y eliminación de registros (quién, cuándo y qué cambió).

## 🚀 Requisitos de Instalación (Desarrollo)

- PHP 8.3 con extensiones: `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `fileinfo`, `curl`, `zip`.
- Composer 2.x.
- PostgreSQL 18.x.
- Node.js 20.x+ y NPM.
- Git.

## ⚙️ Instalación Rápida

```bash
# 1. Clonar el repositorio
git clone [url-del-repositorio]
cd morbilidad-clinica

# 2. Instalar dependencias de PHP
composer install

# 3. Configurar variables de entorno
cp .env.example .env
# Editar .env con los datos de conexión a PostgreSQL

# 4. Generar clave de aplicación
php artisan key:generate

# 5. Ejecutar migraciones y seeders (Catálogos base)
php artisan migrate --seed

# 6. Instalar dependencias de Frontend y compilar
npm install
npm run build

# 7. Iniciar servidor de desarrollo
php artisan serve
```

Acceder al panel en: `http://127.0.0.1:8000/admin`

## 📚 Documentación Adicional

El proyecto incluye documentación técnica detallada en la carpeta `docs/` para el despliegue en el servidor definitivo de la clínica:
- `01_Guia_Despliegue_Servidor.md`: Instalación nativa en Windows (PHP, PostgreSQL, Nginx).
- `02_Configuracion_Nginx_PHP.md`: Configuración del servidor web y PHP-FPM.
- `03_Variables_Entorno_Produccion.md`: Configuración del archivo `.env` para producción.
- `04_Respaldos_Automatizados.md`: Scripts de `pg_dump` y programación de tareas.
- `05_Contingencia_Electrica.md`: Recomendaciones de UPS y recuperación tras cortes.

## 🗺️ Roadmap (Futuras Versiones)
- [ ] Soporte para múltiples sedes en la interfaz (el modelo de datos ya está preparado).
- [ ] Módulo de historias clínicas completas (evolución del paciente).
- [ ] Integración con impresoras térmicas para tickets de triaje.
- [ ] Dashboard epidemiológico con gráficos de tendencia por semana epidemiológica.
