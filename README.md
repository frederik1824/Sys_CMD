# 🛡️ SysCarnet - Precision Logistics & Cloud Sync Hub

SysCarnet es un ecosistema avanzado de gestión logística y carnetización corporativa, ahora evolucionado con una arquitectura **Hybrid-Cloud** de alta integridad.

## 🌟 Características Destacadas

*   **Stratego Sync Center**: Panel de control avanzado para la sincronización bidireccional entre MySQL Local y Firebase Firestore.
*   **Modo Sandbox (Simulation)**: Permite previsualizar el impacto de las sincronizaciones masivas sin alterar datos reales, con una interfaz visual adaptativa (Amber Theme).
*   **Geocoding & Georeferenciación**: Extracción automática de coordenadas desde URLs de Google Maps e integración con Leaflet.js.
*   **Push Success Notifications**: Sistema de notificaciones en tiempo real que confirma la persistencia de datos en la nube tras cada operación CRUD.
*   **Arquitectura RBAC Estricta**: Control de acceso basado en roles (Spatie) con blindaje contra ataques XSS y Optimistic Locking.

---

## 🛡️ Evolución Administrativa (Enterprise Edition)
El sistema ha sido elevado a un estándar de grado empresarial:
- **Permission Matrix**: Interfaz visual para gestión de permisos Spatie agrupada por módulos.
- **Audit Logging System**: Registro de eventos con inspección granular "Old vs New".
- **Impersonation Support**: Soporte para navegar como otro usuario con banner de seguridad.
- **Gamified Operator Profile**: Métricas de rendimiento y efectividad en tiempo real.
- **Role-Based Dashboards**: Dashboards diferenciados para Directivos (Finanzas) y Operadores (Metas).

## 🛠️ Nuevas Funcionalidades (v4.2)

### 🧪 Modo Sandbox
Ubicado en el Centro de Sincronización, este modo blinda el sistema:
- **Ejecución Segura**: Utiliza el flag `--dry-run` para simular descargas (Pull) y subidas (Push).
- **Interfaz Reactiva**: La UI cambia a tonos ámbar y los botones de acción se transforman para indicar que se está en un entorno de pruebas.
- **Auditoría**: Los logs de simulación se marcan permanentemente como "Sandbox" para análisis posterior.

### 🔔 Notificaciones de Persistencia Cloud
Para garantizar la confianza del usuario en el modelo híbrido:
- **Doble Check**: Al actualizar una empresa, el sistema confirma: `✅ Actualizada y respaldada en la nube`.
- **Badge de Integridad**: Un indicador dinámico en el perfil de la empresa muestra el estado exacto de sincronización y el tiempo transcurrido desde el último "Push".

---

## 🚀 Comandos de Sincronización (Artisan)

| Comando | Propósito | Opciones Clave |
| :--- | :--- | :--- |
| `firebase:pull-all` | Descarga masiva desde Firestore | `--full`, `--dry-run`, `--intensity` |
| `firebase:sync-all` | Sincronización diferencial (Push) | `--force`, `--dry-run`, `--companies` |
| `firebase:sync-control` | Gestión de estados de procesos | `pause`, `resume`, `cancel` |

---

## 🏗️ Configuración Técnica

1. **Firebase SDK**: Requiere el archivo de credenciales JSON en la raíz: `syscarnet-firebase-adminsdk-*.json`.
2. **Environment**:
   - `FIREBASE_PROJECT_ID`: ID del proyecto en Google Cloud.
   - `DB_DATABASE`: `sys_carnet` (MySQL 8.x).
3. **Frontend Assets**:
   - `npm run dev` para desarrollo reactivo.
   - `npm run build` para compilación de producción con Vite.

---

## 📅 Actualización: 27 de Abril, 2026 (Decentralized App Architecture)

El sistema ha evolucionado hacia un modelo **Multi-App Descentralizado**, donde cada unidad de negocio opera con autonomía visual y administrativa.

### 🏢 Estructura de Aplicaciones
Se han segregado las interfaces y la lógica de gestión para los tres pilares del sistema:
1.  **Carnetización (ID System)**: Gestión central de identidad, logística y auditoría. (Menú Azul)
2.  **Solicitudes de Afiliación**: Gestión operativa de trámites de ingreso y servicio al cliente. (Menú Índigo)
3.  **Traspasos**: Control de movilidad de afiliados y metas de agentes. (Menú Ámbar)

### 🔑 Gestión de Usuarios y Roles Nativos
- **Segregación de Funciones**: Los administradores de cada módulo solo pueden crear personal con roles "nativos" de su área (ej. Supervisor de Traspasos vs Supervisor de Afiliación).
- **Asignación Automática**: Se garantiza que todo usuario creado tenga acceso inmediato a su aplicación de origen mediante la vinculación forzada de departamento y permisos.
- **Nuevos Roles Operativos**: Implementación de roles específicos como `Agente de Traspasos`, `Analista de Afiliación` y `Servicio al Cliente (CSR)` con sus respectivas matrices de permisos.

### 📁 Gestión de Documentos y Estructura
- **PDF Proxy Service**: Se implementó una ruta de streaming seguro para documentos PDF, resolviendo errores de permisos (`403 Forbidden`) y eliminando la dependencia de enlaces simbólicos en Windows.
- **Estructura Contextual**: El módulo de Departamentos ahora es accesible desde cada aplicación, manteniendo el contexto visual (Sidebar) del módulo activo mediante rutas descentralizadas.

### 🛠️ Mejoras de Estabilidad
- **Base Controller**: Se habilitó el soporte nativo de autorización (`AuthorizesRequests`) para validación estricta de permisos Spatie en todos los módulos.
- **Data Integrity**: Corregidos fallos en relaciones de modelos (`Departamento::users()`) y rutas redundantes que afectaban la navegación.

---

_Documentación actualizada: 27 de Abril, 2026_
_Arquitectura: Decentralized Modular Architecture + Role-Based Context_

