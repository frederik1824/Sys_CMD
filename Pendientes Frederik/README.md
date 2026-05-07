# 🚀 SysCarnet: Comando Stratego & Firebase Sync Engine

**SysCarnet** es una plataforma de misión crítica diseñada para la gestión logística y control de carnetización de ARS CMD. Este sistema implementa una arquitectura **Hybrid-Cloud** (MySQL + Firestore) con sincronización bidireccional en tiempo real.

---

## 💎 Características Principales (Actualizado 22/04/2026)

### 1. Centro de Sincronización "Stratego"
Ubicado en el módulo de Sistema, es el comando central de telemetría y control.
- **Header Inteligente**: Monitoreo de cuotas de lectura/escritura (24h) integrado con el estado de conectividad.
- **Control de Intensidad**: Modos de operación Normal, Turbo y Overdrive para gestionar la velocidad de sincronización.
- **Inspección Quirúrgica**: Side-Drawer integrado para la auditoría en vivo de logs detallados por entidad.
- **Historial Asíncrono**: Paginación mediante AJAX para evitar recargas completas de página.

### 2. Motor de Sincronización Bidireccional (V2)
Implementado a través del Trait `FirebaseSyncable`, permite una coherencia de datos perfecta entre el servidor local y Google Firebase.
- **Push on Save**: Cada cambio realizado en los modelos `Empresa` o `Afiliado` se envía automáticamente a Firebase mediante eventos de Eloquent (`bootFirebaseSyncable`).
- **Pull en Caliente**: Los métodos `show` y `edit` sincronizan los datos desde Firebase antes de renderizar para asegurar que el usuario vea la información más reciente.
- **Sincronización de Catálogos**: Soporte para la bandera `--catalogs` en comandos Artisan, permitiendo actualizar provincias y municipios de forma aislada.

### 3. Inteligencia Geográfica & CRM
Módulo avanzado para la ubicación y gestión de entidades corporativas.
- **Geocodificación Automática**: Extracción inteligente de coordenadas `latitude` y `longitude` a partir de URLs de Google Maps pegadas en el formulario.
- **Visor Leaflet Integrado**: Mapa de alta resolución en la ficha de la empresa para confirmación visual inmediata de la ubicación.
- **Monitor de Densidad**: Heatmap global de afiliados y empresas para análisis de cobertura nacional.

---

## 🛠 Arquitectura Técnica

- **Backend**: Laravel 11.x
- **Frontend**: Blade + Tailwind CSS (iOS Crystal Design System)
- **Base de Datos**: MySQL 8.0 + Google Firestore (REST API via `FirebaseSyncService`)
- **Mapas**: Leaflet.js (OpenStreetMap)
- **Charts**: Chart.js para telemetría de estados y cuotas.

---

## 🚀 Comandos de Operación

### Sincronización Manual
```bash
# Sincronización completa de todos los datos
php artisan firebase:sync-pull --full

# Sincronización exclusiva de catálogos (Provincias/Municipios)
php artisan firebase:sync-pull --catalogs
```

### Gestión de Workers
```bash
# Iniciar procesamiento de colas en segundo plano
./Iniciar_Workers_ARS.bat
```

---

## 📁 Archivos Clave del Módulo
- `app/Traits/FirebaseSyncable.php`: Corazón de la sincronización bidireccional automática.
- `app/Http/Controllers/FirebaseSyncController.php`: Orquestador del Dashboard Stratego.
- `resources/views/firebase/sync-center.blade.php`: Interfaz de comando central.
- `app/Models/Empresa.php`: Implementación de mutadores para geocodificación automática.
- `app/Http/Services/FirebaseSyncService.php`: Driver de comunicación con Google API.

---

## 🛡️ Nuevas Optimizaciones de Seguridad y Feedback (23/04/2026)

### 1. 🏗️ Disaster Recovery Hub (Resiliencia Operativa)
Módulo quirúrgico para la integridad de datos y recuperación ante fallos críticos.
- **Snapshot Manager**: Gestión de respaldos (`z_backup_`) con capacidad de purga automática de versiones antiguas (más de 7 días).
- **Restauración Quirúrgica**: Workflow de "Truncate & Copy" para revertir el sistema a estados previos conocidos en segundos mediante un solo clic.
- **Version Comparator**: Herramienta de auditoría que detecta discrepancias de volumen (Deltas) entre MySQL local y Firestore en tiempo real.

### 2. 🔔 Notification Hub Inteligente (Comando Proactivo)
Centro de notificaciones avanzado con lógica de visualización y gestión en tiempo real.
- **Feedback Event-Driven**: Notificaciones automáticas para subidas/descargas de fondo, limpiezas de caché y estados de recuperación de desastres.
- **Gestión AJAX Fluida**:
    - **Mark-on-Open**: Las notificaciones se marcan como leídas automáticamente al abrir el menú (Visualized State).
    - **Control Total**: Capacidad de eliminación individual y limpieza masiva ("Limpiar todo") sin recargar la página.
    - **Indicadores Visuales**: Sistema de opacidad dinámica para diferenciar mensajes nuevos de visualizados y badge de conteo en tiempo real.

### 3. 📊 Operaciones de Datos Masivas
- **Actualización de Estados**: Ejecución de scripts de alta velocidad para la transición masiva de registros (ej. 126 afiliados marcados como "Acuse recibido" bajo demanda).
- **Consistencia CRM**: Optimización de la ficha de empresa con visor de mapas Leaflet y corrección de bugs de renderizado en métricas de porcentaje.

---

## 🎧 Módulo Call Center Pro & Gestión de Documentos (24/04/2026)

Rediseño integral de la operación de prospección y cierre de casos con enfoque en productividad y trazabilidad documental.

### 1. 📂 Ingestión de Datos "Roberitza" (Agilidad Masiva)
- **Integración Excel Directa**: Soporte para copy-paste directo desde archivos de gestión externa (formato Roberitza) con detección automática de tabulaciones.
- **Validación de Prospección**: Motor de detección de duplicados por Cédula/Nombre antes de la inserción definitiva.
- **Barra de Progreso Premium**: Visualización animada de cargas masivas mediante SweetAlert2 con feedback de registros insertados vs. omitidos.

### 2. 📑 Workflow de Gestión de Cédula (Ciclo de Vida)
- **Estados Granulares AJAX**: Transición fluida de estados sin recarga de página:
    - **Cédula Pendiente**: El afiliado ha sido contactado pero aún no envía el documento (Tray de espera).
    - **Cédula Recibida**: Validación física del documento que dispara el cierre del caso en Call Center.
- **Bandeja de Validación (Salida)**: Hub centralizado para la recepción de documentos, permitiendo a la operadora confirmar la recepción y enviar el registro automáticamente al flujo logístico de carnetización.

### 3. 📈 Dashboard de Producción Diaria (Analytics)
- **Métricas de Efectividad**: Cálculo en tiempo real de la Tasa de Conversión (Éxitos vs. Intentos) y Tasa de Localización.
- **Filtrado Multi-Operador**: Capacidad de auditar la producción individual o colectiva por rangos de fecha.
- **Matriz de Rendimiento**: Desglose porcentual de todos los estados de llamada para identificar cuellos de botella en la contactabilidad.

---

## 📁 Archivos Clave del Proyecto (Core)
- `app/Http/Controllers/Admin/CallCenterController.php`: Cerebro del flujo de prospección y gestión de estados.
- `resources/views/admin/callcenter/worklist.blade.php`: Interfaz operativa principal "Mi Lista Pro".
- `resources/views/admin/callcenter/management.blade.php`: Bandeja de validación documental.
- `resources/views/admin/callcenter/dashboard.blade.php`: Comando de métricas y producción.
- `app/Traits/FirebaseSyncable.php`: Sincronización bidireccional automática.
- `app/Notifications/FirebaseSyncNotification.php`: Motor de alertas del sistema.

---

## ⚡ Fase 2: Optimización de Rendimiento — Eliminación de N+1 Queries (29/04/2026)

Auditoría técnica completa del sistema con enfoque en rendimiento de base de datos. Se eliminaron sistemáticamente todas las consultas N+1 y las queries directas a modelos desde dentro de las vistas Blade, siguiendo el patrón correcto de inyección de datos desde el controlador.

### 1. 🔴 Corrección de Cuellos de Botella en SLA (Controladores)
Los dashboards cargaban colecciones completas de Afiliados en memoria PHP para luego filtrarlas con `->filter()`. Esto causaba agotamiento de memoria con más de 15,000 registros.

**Archivos refactorizados:**
- `app/Http/Controllers/ReporteController.php` — 3 métodos migrados a `COUNT()` SQL con `whereRaw('DATEDIFF(...) >= 20')`
- `app/Http/Controllers/DashboardController.php` — Eliminado `->get()->filter(sla_status)`
- `app/Http/Controllers/ExecutiveDashboardController.php` — Ídem
- `app/Http/Controllers/ExecutiveSuiteController.php` — Ídem

### 2. 🟡 Inyección de Catálogos en Controladores (Eliminación de N+1 en Blade)
Se identificaron y eliminaron todas las llamadas directas `\App\Models\Modelo::all()` o `::where()` dentro de templates Blade. El patrón correcto es pre-cargar los datos en el controlador y pasarlos como variables a la vista.

| Vista | Fix Aplicado |
|---|---|
| `afiliados/index.blade.php` | `$empresas`, `$estados`, `$responsables`, `$cortes` inyectados desde `AfiliadoController@index` |
| `afiliados/show.blade.php` | `$estados`, `$responsables` inyectados desde `AfiliadoController@show` |
| `afiliados/salida_inmediata.blade.php` | 5 queries eliminadas, mismas variables inyectadas |
| `modules/traspasos/index.blade.php` | `AgenteTraspaso::where()` por fila → `$agentesModels` con `keyBy('nombre')` |
| `modules/traspasos/config/agentes.blade.php` | `MetaTraspaso::where()` en cada fila → relación `currentMeta()` con eager loading |
| `admin/callcenter/management.blade.php` | 4 llamadas a `Estado::where()` en un mismo render → colección `$estadosCc` pre-cargada |
| `empresas/enrich.blade.php` | Query `Empresa::whereNotNull()->count()` → variable `$enriched` |
| `despachos/create_batch.blade.php` | `Afiliado::pluck()->unique()` → variable `$provincias` pre-ordenada |
| `livewire/intranet/document-manager.blade.php` | 4 queries → variables `$departments`, `$documentTypes`, `$documentStatuses` del `render()` |
| `modules/afiliacion/show.blade.php` | `Afiliado::where('cedula')` dentro de `@php` → `$afiliadoMaestro` inyectado |

### 3. 🟢 Optimizaciones de Modelos y Relaciones
- **`AgenteTraspaso`**: Añadidas relaciones `metas()` y `currentMeta()` para soporte de eager loading correcto desde `AgenteController@index`, eliminando 1 query por agente en la tabla de configuración.
- **`AgenteController`**: `with('supervisor')` actualizado a `with(['supervisor', 'currentMeta'])`.

### 4. 🐛 Bugfix: Ruta `usuarios.create` no definida
- **Causa**: `CMD\UserController` tenía `$routePrefix = 'usuarios'` pero las rutas reales están registradas como `sistema.usuarios.*`.
- **Fix**: Corregido a `$routePrefix = 'sistema.usuarios'`. La vista `module_index.blade.php` construye todas las rutas dinámicamente (`$routePrefix . '.create'`, etc.), por lo que el fix es centralizado y sin tocar las vistas.

### 5. 🧹 Limpieza General
- `php artisan view:clear` — Vistas compiladas limpias
- `php artisan cache:clear` — Caché de aplicación limpia
- `php artisan config:clear` — Configuración limpia

---

## 📊 Business Intelligence & Sales Performance (01/05/2026)

Esta fase marca la entrega definitiva de la suite analítica de Traspasos, transformando datos crudos en inteligencia comercial procesable para la supervisión.

### 1. 🚀 Suite de Producción de Traspasos (Los 5 Pilares)
Implementación de un modelo de análisis transaccional de alta precisión que desglosa la producción en tiempo real:
- **Titulares Efectivos**: Conteo neto de contratos cerrados basados en `fecha_efectivo` dentro del rango seleccionado.
- **Cant. Dependientes**: Volumen de dependientes vinculados exclusivamente a esos cierres efectivos.
- **Vidas Totales**: El output real de la gestión comercial (Titulares + Dependientes).
- **Pendientes de Efectividad**: Seguimiento del backlog de casos solicitados en el periodo que aún no han cerrado.
- **Traspasos Rechazados**: Auditoría de calidad de prospección y casos fallidos.

### 2. 🔄 Rebranding Integral & Estabilidad de Contexto
- **Adiós "Vidas"**: Eliminación total del término legado "Vidas" en todo el sistema (Controladores, Vistas, Reportes y Exportaciones), unificando la identidad bajo **Traspasos**.
- **UX Continuity**: Se corrigió el error de "salida de app". Ahora el layout principal (`app.blade.php`) reconoce dinámicamente las rutas de reportes como parte del ecosistema de Traspasos, manteniendo la barra lateral y el contexto visual activo.
- **Sincronización de Equipos**: Integración total con el modelo `SupervisorTraspaso`, permitiendo filtrados precisos por equipo en toda la suite analítica.

### 3. 🛠️ Bugfixes de Estabilidad
- **Fix Error 500 (Dashboard)**: Corregido error de columna `estado` inexistente mediante la normalización a `estado_id`.
- **Fix Error 500 (Blade)**: Resolución de claves de array indefinidas en el resumen de estadísticas (`total_dependientes_efectivos`).
- **Data Integrity**: Sincronización de exportaciones CSV para utilizar relaciones de Eloquent en lugar de atributos legacy.

---

## 📁 Archivos Clave de la Sesión (BI)
- `app/Http/Controllers/ReporteController.php`: Lógica de análisis de producción transaccional.
- `resources/views/reportes/produccion_traspasos.blade.php`: Interfaz premium con métricas de 5 pilares y Chart.js.
- `resources/views/layouts/app.blade.php`: Lógica de contexto de módulo y branding dinámico.

---

## 📞 Consola Premium de Call Center V2 (High-Performance Console) — 05/05/2026

Transformación del módulo de Call Center en un centro de operaciones de alto rendimiento "Premium", enfocado en la eficiencia del operador y la integridad de los datos.

### 1. 🏗️ Arquitectura de Aislamiento y Control (Stage Architecture)
- **Separación de Datos**: Implementación de 8 tablas dedicadas (`call_center_*`) que aíslan los prospectos de la base maestra de `afiliados` hasta que son validados y promovidos.
- **Trazabilidad Total**: UUIDs únicos por registro, historial de gestiones inalterable y repositorio documental integrado.
- **Validación en Tiempo Real**: El sistema omite automáticamente prospectos que ya están marcados como "Completados" en el sistema maestro de carnetización.

### 2. ⚡ Motor de Ingestión por Lotes (Batch Engine)
- **Importación con Progreso Real**: Rediseño del motor de importación que procesa la data en **lotes de 10 registros**.
- **Barra de Progreso Dinámica**: Interfaz visual con porcentaje de avance y contadores en vivo de registros Nuevos, Actualizados y Omitidos.
- **Optimización de Memoria**: El procesamiento segmentado evita bloqueos del navegador y tiempos de espera del servidor (timeouts).

### 3. 🎯 Consola de Gestión Premium (3-Column Layout)
- **Diseño de Triple Columna**: Distribución de alto impacto inspirada en CRMs de clase mundial.
    - **Izquierda**: Perfil del prospecto y botones de **Acción Rápida** (No contesta, Ocupado) de un solo clic.
    - **Centro**: Formulario de interacción con sincronización opcional a la Empresa Maestra y Timeline de historial.
    - **Derecha**: Scripts de venta dinámicos (Sticky) y expediente documental.
- **Seguimiento Inteligente**: Bandeja de trabajo con alertas visuales (pulsaciones) para seguimientos pendientes, vencidos o para el día de hoy.

### 4. 🚀 Integración Estratégica (The Bridge)
- **Promoción Blindada**: El botón de "Promover" se bloquea automáticamente una vez procesado el registro para evitar duplicidad en carnetización.
- **Contexto Empresarial**: Panel de "Otros en esta Empresa" que permite al operador gestionar múltiples empleados de una misma empresa en una sola llamada.
- **Smart-Sync de Datos**: Sincronización selectiva de teléfonos y direcciones actualizadas con la ficha corporativa de la empresa.

---

## 📁 Archivos Clave del Módulo Call Center V2
- `app/Http/Controllers/Modules/CallCenter/CallCenterController.php`: Orquestador de la lógica de lotes, KPIs y promoción.
- `resources/views/modules/call_center/manage.blade.php`: Interfaz premium de gestión 360°.
- `resources/views/modules/call_center/worklist.blade.php`: Bandeja operativa con filtros avanzados y alertas de seguimiento.
- `resources/views/modules/call_center/import.blade.php`: Motor de ingesta por lotes con UI de progreso.

---

_Documentación actualizada: 05 de Mayo, 2026_
_Fase: Finalización de Consola de Operaciones Premium y Motor de Ingestión Segmentada_
