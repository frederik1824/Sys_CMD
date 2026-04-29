# 📦 Handover: Transferencia de Contexto de Desarrollo

Si vas a continuar este proyecto en otro equipo o con otra IA, **copia y pega el contenido de este documento** al inicio de la nueva conversación. Esto permitirá retomar el trabajo exactamente donde lo dejamos.

---

## 🏗️ Estado Actual del Proyecto: SysCarnet
- **Objetivo**: Sincronización bidireccional eficiente con Firebase Firestore (Plan Spark).
- **Tecnologías**: Laravel 11, PHP 8.3, Firebase REST API (evitando dependencias pesadas de Google Cloud SDK).
- **Base de Datos**: MySQL con soporte para UUIDs en tablas principales.

## ✅ Funcionalidades Implementadas (Sesión Actual)

### 1. Arquitectura "Delta Sync" (Incremental)
- Hemos pasado de descargas completas a un modelo incremental.
- El sistema guarda la fecha del último éxito en `firebase_sync_logs` y solo descarga lo que ha cambiado (`updated_at` en Firebase).
- **Beneficio**: Ahorro crítico de cuota de lectura (Error 429 evitado).

### 2. Sync Center Cloud (Dashboard)
- Panel visual interactivo en `/firebase/sync-center`.
- **Controles en Tiempo Real**: Botones de **Pausar**, **Reanudar** y **Cancelar**.
- **Métricas Vivas**: Contadores dinámicos de registros procesados durante la sincronización.
- **Autorefresh**: La página se recarga sola al finalizar el proceso.

### 3. Comandos Artisan
- `php artisan firebase:sync-pull [--full]`: Descarga Cloud -> Local.
- `php artisan firebase:push-all [--force]`: Sube Local -> Cloud.
- Ambos comandos soportan la opción `--log-id` para vinculación con la UI.

## 📁 Archivos Clave
- `app/Services/FirebaseSyncService.php`: Corazón de la comunicación REST.
- `app/Console/Commands/FirebaseSyncPull.php`: Lógica de descarga e incrementalidad.
- `app/Console/Commands/FirebaseSyncAll.php`: Lógica de subida diferencial.
- `app/Http/Controllers/FirebaseSyncController.php`: Gestión de estados de control y progreso.
- `resources/views/firebase/sync-center.blade.php`: UI interactiva.

## 🛠️ Requisitos de Reinicio
- Asegurar que el archivo `.env` tenga `FIREBASE_CREDENTIALS` apuntando al JSON correcto.
- **Importante**: Siempre tener abierta una terminal con `php artisan queue:work` para procesar los Jobs de sincronización.

## 📋 Pendientes / Próximos Pasos
1. **Monitoreo de Cuota**: Observar si la cuota de 50k lecturas diarias es suficiente tras los cambios.
2. **Automatización**: Configurar el `Schedule` para ejecutar el Pull incremental automáticamente cada noche.
3. **Optimización de Push**: Actualmente el Push revisa registros uno a uno; para volúmenes masivos se podría optimizar con peticiones "Batch".

---
**Instrucción para la siguiente IA**: "Hola, soy desarrollador de SysCarnet. He adjuntado el documento de Handover. Por favor, léelo y confírmame que entiendes la arquitectura de Delta Sync y el sistema de control de FirebaseSyncController antes de continuar."
