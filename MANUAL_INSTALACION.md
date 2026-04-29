# Manual de Instalación y Configuración - SysCarnet

Este manual describe los pasos necesarios para desplegar el sistema **SysCarnet** en una nueva máquina (Windows con XAMPP recomendado).

## 1. Requisitos Previos

Asegúrate de tener instalados los siguientes componentes:

*   **PHP**: Versión 8.3 o superior.
*   **Composer**: Gestor de dependencias de PHP.
*   **Node.js & NPM**: Versión LTS recomendada (para Vite y componentes del frontend).
*   **Servidor Web y Base de Datos**: XAMPP con Apache y MySQL/MariaDB.

## 2. Preparación de la Base de Datos

1.  Abre el panel de control de **XAMPP** e inicia los servicios de **Apache** y **MySQL**.
2.  Accede a `phpMyAdmin` (usualmente `http://localhost/phpmyadmin`).
3.  Crea una nueva base de datos llamada `sys_carnet` con el cotejamiento `utf8mb4_unicode_ci`.

## 3. Configuración del Proyecto

Abre una terminal (PowerShell o CMD) en la carpeta raíz del proyecto (`sys_carnet`) y sigue estos pasos:

### A. Instalación de dependencias de PHP
```bash
composer install
```

### B. Configuración del archivo de entorno
Crea una copia del archivo `.env`:
```bash
copy .env.example .env
```
Luego, abre el archivo `.env` y verifica los siguientes datos de conexión (ajusta según tu configuración de XAMPP):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306  # Cambia a 3307 si tu XAMPP usa ese puerto
DB_DATABASE=sys_carnet
DB_USERNAME=root
DB_PASSWORD=
```

### C. Generación de clave de aplicación
```bash
php artisan key:generate
```

### D. Migración de Base de Datos
Este comando creará todas las tablas necesarias:
```bash
php artisan migrate
```

### E. Migración de Datos de Empresas (Opcional si es base limpia)
Si tienes datos previos de afiliados y quieres generar la tabla maestra de empresas:
```bash
php artisan carnet:migrate-empresas
```

## 4. Configuración del Frontend

Instala las dependencias de Node.js y compila los assets:

```bash
npm install
npm run build
```

## 5. Ejecución del Sistema

Para ejecutar el sistema localmente, necesitas tener dos procesos corriendo (puedes usar el comando integrado o abrir dos terminales):

**Opción rápida:**
```bash
php artisan serve
```
Y en otra terminal:
```bash
npm run dev
```

El sistema estará accesible en: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---
**Nota sobre permisos**: Si el sistema no muestra el módulo de "Empresas", asegúrate de que tu usuario tenga asignado el rol de **Administrador** o **Supervisor** en la base de datos.


Documentacion:

# Documentación Técnica ARS CMD: Sistema de Gestión Logística y Carnetización

> [!IMPORTANT]
> **Aviso de Confidencialidad y Uso**
> Este documento representa la Arquitectura Core y el Manual Técnico oficial de ARS CMD (Sys_Carnet). Su contenido está dirigido exclusivamente a la Gerencia Técnica, Auditoría Interna, y al Equipo de Desarrollo. 

---

## 2.1 Resumen Ejecutivo

### Propósito del Sistema
ARS CMD es un sistema integral tipo ERP/CRM diseñado para orquestar de principio a fin el proceso de admisión, logística, asignación, seguimiento, y liquidación de afiliaciones y carnetizaciones corporativas. 

### Problema que Resuelve
Anteriormente, la gestión de carnets y afiliaciones carecía de trazabilidad estricta, enfrentando problemas de concurrencia, pérdida de evidencias, cuellos de botella en despachos logísticos, y vulnerabilidades en la asignación de responsables. El sistema resuelve esto introduciendo flujos de trabajo rígidos, auditoría inmutable, y un motor de logística georreferenciado.

### Alcance y Principales Funcionalidades
- **Admisión Automatizada:** Carga y validación transaccional de lotes de afiliados.
- **Motor Logístico Integrado:** Creación de rutas, asignación a mensajeros y tracking de despachos.
- **Cierre y Liquidación Óptica:** Gestión documental (acuse y formularios) ligada obligatoriamente a cierres de estado.
- **Seguridad Empresarial:** RBAC estricto, blindaje Anti-XSS, Locking Optimista y llaves UUID no predecibles.

---

## 2.2 Arquitectura del Sistema

### Tipo de Arquitectura
El sistema se rige bajo una arquitectura **MVC Evolucionada hacia Capas de Servicio (Service-Oriented MVC)** sobre el framework Laravel. Separa las responsabilidades estrictamente para evitar "Controladores Gordos", aislando la lógica de negocio pura en clases de Servicio y delegando la abstracción de datos a Eloquent ORM.

### Capas del Sistema
1. **Presentation (Presentación):** Motor de plantillas Blade optimizado con Tailwind CSS oscuro/premium, complementado con Livewire (para componentes reactivos) y Alpine.js (gestión de estado micro-frontend).
2. **Application (Aplicación - Controllers):** Recepción de Request HTTP, validación de FormRequests (incluyendo Optimistic Locking), delegación a Servicios y retorno de Respuestas.
3. **Domain (Servicios de Dominio):** Clases puras (ej. `LogisticaService`, `AfiliadosManager`) que dictan las reglas lógicas (Transiciones de máquina de estado, SLAs).
4. **Infrastructure (Infraestructura):** Integrado por Middleware de Seguridad (`SanitizeInput`, Spatie Permission), Traits globales (`Auditable`), Webhooks, ORM (Eloquent), y Bases de Datos (MySQL).

### Diagrama Conceptual y Flujo de Datos
`[Cliente Web]` ➔ `[Middleware Anti-XSS / RBAC]` ➔ `[Router]` ➔ `[FormRequest (Validación + Lock Check)]` ➔ `[Controller]` ➔ `[Service Layer (DB Transaction)]` ➔ `[Model/ORM]` ➔ `[Database]`

> [!TIP]
> **Decisión Arquitectónica Clave:** Se eliminó la dependencia de IDs secuenciales enrutables y se implementó un sistema basado en UUIDv4 para todas las entidades transaccionales (Afiliados, Empresas). Esto previene scraping de base de datos (Insecure Direct Object Reference - IDOR).

---

## 2.3 Modelo de Dominio

### Entidades Core
* **Afiliado (`afiliados`)**: Corazón operativo.
  * **Atributos:** UUID, Cédula (Llave natural, con sanitización personalizada de guiones), Nombre completo, Estado, SLAs.
  * **Reglas:** No puede retroceder estados logísticos, sujeto a auditoría "before/after".
* **Empresa (`empresas`)**: Entidad pagadora / solicitante.
  * **Atributos:** RNC/Cedula, Razón Social, `promotor_id` (Usuario del sistema, reemplazó al obsoleto modelo *Responsable*).
* **Usuario (`users`)**: Eje de seguridad.
  * **Configuración:** Emplea Spatie Permissions (`roles`, `permissions`). Actúa como promotores, mensajeros, o auditores.
* **Geografía (`provincias`, `municipios`)**:
  * **Propósito:** Normalización de direcciones para cálculo de rutas logísticas y el Mapa Global de Calor (Heatmap).
* **Logística (`lotes`, `rutas`, `despachos`)**:
  * **Reglas:** Un lote agrupa `N` afiliados. Un despacho rastrea el tránsito de los lotes mediante un mensajero específico.

---

## 2.4 Modelo de Base de Datos

### Estrategia de Diseño y Normalización
La DB está diseñada en 3FN (Tercera Forma Normal) apoyada fuertemente por restricciones de llave foránea (Foreign Keys Constraints) en cascada de protección (Restrict on Delete).

- **Uso de UUIDs:** Tablas como `empresas` y `afiliados` utilizan la columna `id` como `char(36)` primaria transaccional.
- **Locking Optimista:** Implementación de columnas `last_updated_at` (Timestamp) en lugar de depender únicamente de bloqueos pesados de DB, solucionando problemas de sobreescritura paralela.
- **Geografía Resoluble:** Para mantener integridad y soportar reportes geoespaciales, se migraron los textos libres a IDs foráneos apuntando al catalogador `provincias` y `municipios`. *(Se introdujo el `GeograficaNormalizerSeeder` para procesar data legacy).*
- **Soft Deletes:** Preservación histórica mediante `deleted_at`. Los registros nunca vuelan (DELETE duro) a menos de una purga ejecutada por sysdba.

---

## 2.5 Reglas de Negocio

> [!WARNING]
> La violación de estas reglas a nivel de base de datos se encuentra restringida mediante Bloqueos Transaccionales (DB::transaction).

1. **Máquina de Estados de Afiliados:** 
   *(Admitido ➔ Asignado Ruta ➔ Despechado ➔ Entregado ➔ Documentado ➔ Liquidado/Cerrado).* No se permiten saltos cuánticos, excepto incidencias documentadas.
2. **Cierre de Evidencias (Documentos):** Un afiliado **jamás** transiciona a `Cerrado` sin validar en sistema la subida digital de (1) Acuse de recibo y (2) Formulario firmado.
3. **Bloqueo Secuencial (Optimistic Locking):** Si el Usuario A y Usuario B abren la edición de un Afiliado, y Usuario A guarda, el Usuario B recibirá una excepción tipo *Concurrencia detectada* impulsada por el mismatch en `last_updated_at`.
4. **Asignación Promotor:** (Reemplazo Legacy). Ya no existen "responsables" libres. Todo afiliado / empresa hereda un `promotor_id` estricto apuntando a un usuario autenticado del sistema.
5. **Anti-Duplicidad de Cédulas y RNC:** Las cédulas ignoran guiones para validación (`replace('-', '')`), asegurando un `unique` nativo consistente.

---

## 2.6 Flujos Funcionales

### Flujo 1: Carga Masiva (Admisión)
1. **Input:** Usuario sube Excel/CSV.
2. **Validación:** El sistema verifica formatos estandarizados y comprueba duplicados vía cédula sanitizada.
3. **Estado Resultante:** Lote creado. Afiliados en estado `Pendiente`.

### Flujo 2: Despacho y Logística
1. **Agrupamiento:** Supervisor agrupa Afiliados de una zona en una Ruta.
2. **Asignación:** Se emite un "Despacho" asignado a un Usuario con rol `Mensajero`.
3. **Estado Resultante:** Afiliado `En Ruta`.

### Flujo 3: Cierre Documental y Liquidación
1. **Recepción:** Mensajero retorna y Operador escanea evidencias (PDF/JPG).
2. **Validación (Backend):** `EvidenciasService` verifica peso y limpieza del archivo, inyectando meta-tags.
3. **Cierre:** Transición final de estado a `Liquidado`. Se estampa el timestamp `fecha_entrega_safesure`.
4. **Bloqueo:** Interfaz de edición se "congela" (Read Only) basándose en política `@cannot('edit_liquidated')`.

---

## 2.7 API & Endpoints Internos

Al ser una arquitectura MVC estructurada con interfaces blade, no es una API RESTful pública. No obstante, las rutas web están fuertemente estandarizadas (Resourceful Constraints):

- `GET /afiliados/{afiliado:id}` (Manejo automático del UUID por Eloquent Binding).
- `PATCH /empresas/{empresa:id}` (Punto crítico donde interviene el Request de Optimistic Locking).
- **Formatos de Respuesta:** Respuestas estándar HTTP (302 Redirect con variables de sesión `success/error`). En submódulos o validaciones dinámicas (ej. Búsqueda TopBar) se utiliza JSON sobre endpoints protegidos con middleware `web`.

---

## 2.8 Servicios y Lógica de Aplicación

Para facilitar mantenimiento, existe segregación en `app/Services/`:

- **`InputSanitizationMiddleware` (Global):** Intercepta `$request->all()`. Limpia etiquetas XSS (`strip_tags`), remueve acentos para normalización cuando aplique, y realiza trim profundo. Tiene exceptions para campos como `cedula` (preserva formato numérico para validación posterior natural).
- **Trait `Auditable`:** Anclado a los Modelos. Escucha eventos `updating/deleting`. Registra en crudo el payload `old_values` y `new_values`, mapeándolo transparentemente al modelo `AuditLog`. Relaciona el `user_id` en tiempo real.

---

## 2.9 Seguridad y Permisos (RBAC)

> [!CAUTION]
> **Modelo Deny-All (Denegar por Defecto):** A nivel de routing (`web.php`), el sistema enrutador envuelve TODOS los módulos bajo grupos de middleware. Si un usuario no tiene un permiso explícito, obtiene un `HTTP 403 Forbidden` absoluto.

**Arquitectura de Permisos (Spatie):**
- Roles base: `Super-Admin`, `Logistica-Manager`, `Operador-Admision`, `Auditor`.
- Permisos granulares: `access_admin_panel`, `manage_users`, `manage_companies`, `manage_affiliates`, `manage_evidencias`, `manage_logistics`, `manage_closures`, `manage_liquidations`, `view_reports`.
- **UI Reactiva:** La navegación lateral (`app.blade.php`) computa directivas `@can` estáticas; si el rol no coincide, el HTML del enlace no viaja en el payload de la vista.

---

## 2.10 Auditoría y Trazabilidad

**Estructura del `AuditLog`:**
El sistema responde a estricta fiscalización.
- **Tabla:** `audit_logs` (id, user_id, action, auditable_type, auditable_id, old_values, new_values, ip_address, created_at).
- **Interfaz (Auditoría):** Vista especializada para usuarios con rol `Auditor` o permiso `access_admin_panel`. Permite reversar o comprender quien causó un desfase en la data de un afiliado.
- **Identidad Autónoma:** Se incluye tracking nativo de la Capa de Sesión (Registro de IPs, fechas del último inicio de sesión en el Modelo User).

---

## 2.11 Módulo de Reportes

Orientado a la métrica técnica y operativa, protegido bajo `view_reports`.
- **Heatmap (Mapa de Calor Logístico):** Cruce masivo relacional entre `Afiliados` -> `Provincia/Municipio`.
- **Alertas SLA (Service Level Agreement):** Lógica que mide la brecha algorítmica entre la fecha de creación y `fecha_entrega_safesure`.
- **Comparativas de Rendimiento:** KPIs por Promotor (Basado en el nuevo link entre Entidad y User_id).

---

## 2.12 Instalación y Configuración

**Requisitos Tecnológicos:**
- PHP >= 8.2 (Estricto tipeo)
- MySQL 8.x
- Composer, Node.js (Vite)

**Paso a paso:**
1. `git clone` del repositorio oficial.
2. `composer install --no-dev --optimize-autoloader`
3. `npm install && npm run build` (Para empaquetar Tailwind y Vite).
4. **Entorno:** Configurar `.env` verificando credenciales DB y asegurando `APP_DEBUG=false` en prod.
5. **Migraciones:** `php artisan migrate:fresh`
6. **Seeders:** IMPORTANTE ejecutar: `php artisan db:seed --class=RolesAndPermissionsSetupSeeder` para blindar el RBAC, seguido del `GeograficaNormalizerSeeder`.
7. **Symlink:** `php artisan storage:link` para evidencias documentales.

---

## 2.13 Despliegue (Deployment)

> [!TIP]
> Dado el volumen transaccional de PDFs y reportes de calor, el entorno de producción debe contar con Worker Daemons.

- **Workers (Colas):** Se recomienda usar `php artisan queue:work` corriendo bajo *Supervisor* en Linux si se habilitan colas para importes masivos por excel.
- **Optimizaciones mandatorias pre-deploy:**
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
- **Backups:** Implementar backups a nivel crontab de la tabla `audit_logs` y volumen `/storage/app/evidencias`.

---

## 2.14 Buenas Prácticas y Mantenimiento

Para el onboarding de **Nuevos Desarrolladores**:
1. **Extensión del Sistema:** Las reglas del modelo (SLA, verificaciones) van en sus Request u Observers, *NO* en el Controlador.
2. **Modificación de Roles:** Nunca editar permisos directamente por base de datos. Utilizar el módulo de "Usuarios & Roles" o modificar y correr el `RolesAndPermissionsSetupSeeder`.
3. **Nuevas Vistas:** Mantener estricta coherencia con la librería *Phosphor Icons* y el panel oscuro premium implementado en `app.blade.php`.
4. **Evitar Scopes sucios:** Utilice Query Builders de Laravel o Scopes de modelo encapsulados al invocar data para reportes.

---

## 2.15 Limitaciones Actuales y Deuda Técnica

1. **Storage Local:** Las evidencias en la actualidad caen al disco local. Si la escalabilidad salta, migrar a S3 o un `Bucket` será obligatorio en `/config/filesystems.php`.
2. **Workers de Envío Masivo:** Las notificaciones al cliente actualmente están acopladas al hilo HTTP (Síncrono). Deben moverse a Jobs Asíncronos.
3. **Soft Deletes en cascada:** Aunque activos, las relaciones complejas requieren restauración manual.

---

## 2.16 Roadmap Técnico

### Fase Q3 - Q4
1. **Pluggability API:** Generación de Oauth2 (Laravel Passport/Sanctum) para interconectar con escáneres Logísticos de Móviles App.
2. **Data Connect AI:** Integrar herramientas de predicción para agrupar rutas y reducir costos de mensajeros basándose en las coordenadas registradas en el mapa global.
3. **Firmas Digitales PAdES:** Mejorar el PDF builder asegurando la integridad del acuse de recibo de los afiliados de alto riesgo.

---
_Documento Aprobado por la Jefatura de Arquitectura de Software_
_Framework Versión: Laravel 11.x_
_Generado: Marzo 2026_

