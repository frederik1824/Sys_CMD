# MANUAL TÉCNICO Y OPERATIVO DEL SISTEMA (CMD)
**Arquitectura de Integración API REST & Monitoreo Real-Time**

---

## 1. Portada

**Sistema:** Central Management Dashboard (CMD)  
**Versión:** 4.2 (Enterprise Edition)  
**Fecha:** 24 de Abril de 2026  
**Departamento Responsable:** Gerencia de Tecnología / Operaciones Logísticas  
**Documento:** Manual de Usuario y Administración Técnica  

---

## 2. Índice

1.  [Introducción](#3-introducción)
2.  [Descripción General del Sistema](#4-descripción-general-del-sistema)
3.  [Arquitectura Técnica](#5-arquitectura-técnica)
4.  [Funcionalidades del Sistema](#6-funcionalidades-del-sistema)
5.  [Módulo de Monitoreo (Dashboard)](#7-módulo-de-monitoreo-dashboard)
6.  [Estados del Sistema](#8-estados-del-sistema)
7.  [Flujo de Operación del Sistema CMD](#9-flujo-de-operación-del-sistema-cmd)
8.  [Manejo de Errores](#10-manejo-de-errores)
9.  [Seguridad](#11-seguridad)
10. [Requisitos del Sistema](#12-requisitos-del-sistema)
11. [Instalación y Configuración](#13-instalación-y-configuración)
12. [Uso del Sistema](#14-uso-del-sistema)
13. [Mantenimiento](#15-mantenimiento)
14. [KPIs y Métricas](#16-kpis-y-métricas)
15. [Glosario](#17-glosario)
16. [Anexos](#18-anexos)

---

## 3. Introducción

### Descripción del Sistema CMD
El sistema **CMD (Central Management Dashboard)** es la plataforma núcleo diseñada para la gestión logística y el control de flujos de datos corporativos. Actúa como el origen de la información y el orquestador principal de las comunicaciones hacia servicios externos.

### Propósito del Módulo de Sincronización
Este módulo garantiza que cada acción realizada en la base de datos local sea replicada de manera segura y eficiente hacia el servicio externo, proporcionando visibilidad total del estado de cada transacción.

### Alcance del Sistema
Desde la captura de datos del afiliado hasta la confirmación de entrega en el destino final, CMD controla el ciclo de vida completo del dato, gestionando errores y garantizando la integridad de la información.

### Público Objetivo
- Personal técnico (Desarrolladores, DevOps).
- Personal operativo (Supervisores de Logística, Administradores de Sistema).
- Auditores de cumplimiento de datos.

---

## 4. Descripción General del Sistema

### Rol de CMD dentro de la Arquitectura
CMD se posiciona como el **Sistema Origen (Source of Truth)**. Todas las transacciones se originan en su base de datos relacional y son despachadas mediante una capa de integración hacia el exterior.

### Integración mediante API REST
La comunicación se realiza a través de un bus de servicios basado en protocolos HTTP/JSON. CMD consume endpoints específicos para el envío y actualización de registros, actuando como un cliente de alta disponibilidad.

### Uso de Firebase como Intermediario de Monitoreo
Firebase Firestore no actúa como base de datos primaria, sino como un **Canal de Espejo y Monitoreo**. Permite que las interfaces de usuario (Dashboards) reflejen cambios en milisegundos sin sobrecargar la API REST principal, funcionando como un centro de notificaciones en tiempo real.

---

## 5. Arquitectura Técnica

### Componentes del Sistema CMD
- **Core**: Laravel v11.x (PHP 8.3).
- **Base de Datos**: MySQL 8.x para persistencia relacional.
- **Cache & Real-time**: Firebase Firestore.
- **Task Runner**: Sistema de colas (Queues) para envíos asíncronos.

### Flujo Interno de Datos
1. Una acción ocurre en CMD (ej. Cambio de estado).
2. Se registra el cambio en la auditoría local.
3. Se dispara un evento asíncrono para el envío vía API REST.
4. Se actualiza el nodo correspondiente en Firebase para visibilidad del dashboard.

### Métodos HTTP Utilizados
- `POST`: Creación de nuevos registros en el sistema externo.
- `PUT / PATCH`: Actualización de estados o metadatos existentes.
- `GET`: Sincronización de catálogos y validación de existencia.

---

## 6. Funcionalidades del Sistema

- **Envío de Datos**: Despacho automático de expedientes completos al servicio externo.
- **Actualización de Registros**: Sincronización de cambios parciales (ej. Direcciones, Teléfonos).
- **Registro de Eventos (Logs)**: Auditoría granular que almacena el payload enviado y la respuesta recibida.
- **Manejo de Errores**: Captura de excepciones HTTP (4xx, 5xx) con lógica de tipificación.
- **Reintentos Automáticos**: Sistema de "backoff" para reintentar envíos fallidos por problemas de red.
- **Consulta de Historial**: Acceso a la línea de tiempo de cada registro enviado.

---

## 7. Módulo de Monitoreo (Dashboard)

### Descripción de la Interfaz
El centro de control es una SPA (Single Page Application) reactiva que consume datos directamente de Firebase para evitar latencia.

### KPIs Mostrados
- **Volumen Total**: Cantidad de registros en el flujo actual.
- **Efectividad**: Porcentaje de registros exitosos vs fallidos.
- **Latencia de Sincronización**: Tiempo promedio de respuesta de la API externa.
- **Pendientes Críticos**: Registros que requieren intervención manual.

### Estados de los Registros
Se visualizan mediante códigos de colores:
- **Verde**: Exitoso.
- **Ámbar**: En proceso / Reintentando.
- **Rojo**: Error crítico.

---

## 8. Estados del Sistema

| Estado | Definición |
| :--- | :--- |
| **Pendiente** | El registro ha sido creado en CMD pero aún no entra en la cola de envío. |
| **En Proceso** | El registro ha sido tomado por el worker y está siendo procesado por la API REST. |
| **Enviado** | Confirmación de recepción exitosa por parte del servicio externo (HTTP 200/201). |
| **Error** | Falla en el envío (Validación de datos, caída de servicio, etc.). |
| **Reintentado** | El sistema detectó un error temporal y está ejecutando un segundo intento. |
| **Cancelado** | El registro ha sido retirado del flujo de envío por decisión administrativa. |

---

## 9. Flujo de Operación del Sistema CMD

1.  **Generación**: El operador genera el registro en CMD.
2.  **Validación**: CMD verifica que el payload cumpla con los requisitos del servicio externo.
3.  **Persistencia Local**: Se guarda en MySQL.
4.  **Despacho**: Se envía vía API REST.
5.  **Reflejo en Firebase**: Se actualiza el estado en tiempo real para el monitoreo.
6.  **Confirmación**: Se recibe el ID de confirmación del externo y se cierra la transacción en CMD.

---

## 10. Manejo de Errores

### Causas Comunes
- **Errores de Red**: Timeouts o pérdida de conexión con el endpoint externo.
- **Errores de Datos**: Campos obligatorios faltantes o formatos incorrectos (ej. Cédula inválida).
- **Límite de Cuota**: Superación de los límites de peticiones permitidos por la API.

### Proceso de Reintentos
CMD implementa una política de hasta 3 reintentos para errores 5xx (servidor) y detiene el proceso para errores 4xx (cliente) que requieran corrección manual de datos.

---

## 11. Seguridad

- **Autenticación Bearer**: Uso de tokens JWT para cada petición hacia la API REST.
- **Cifrado TLS**: Toda la comunicación se realiza sobre HTTPS.
- **Control de Roles**: Solo administradores autorizados pueden forzar reintentos o editar configuraciones de endpoints.
- **Variables de Entorno**: Las credenciales de Firebase y API están protegidas en archivos `.env` fuera del código fuente.

---

## 12. Requisitos del Sistema

- **Servidor**: Linux (Ubuntu 22.04+ recomendado) o Windows Server con IIS.
- **Motor**: PHP 8.2+ con extensiones `bcmath`, `curl`, `mbstring`, `openssl`.
- **Memoria**: Mínimo 2GB RAM para procesos de worker asíncronos.
- **Conectividad**: Acceso a internet para comunicación con Firebase y API Externa.

---

## 13. Instalación y Configuración

1.  **Entorno**: Configurar el archivo `.env` con las rutas de API.
2.  **Credenciales**: Instalar el archivo `json` de service account para Firebase.
3.  **Dependencias**: Ejecutar `composer install` y `npm install`.
4.  **Worker**: Iniciar el proceso `php artisan queue:work` para procesar envíos.

---

## 14. Uso del Sistema

- **Dashboard**: Acceder vía menú lateral `Sistema > Sync Center`.
- **Filtros**: Utilizar la barra de búsqueda para localizar expedientes por cédula o lote.
- **Reintentos Manuales**: En caso de error crítico, usar el botón "Reintentar" en el detalle de la transacción.

---

## 15. Mantenimiento

- **Monitoreo Continuo**: Revisar semanalmente los logs de errores acumulados.
- **Limpieza de Logs**: Ejecutar procesos de purga para registros de auditoría mayores a 30 días.
- **Actualización de Tokens**: Renovar credenciales de acceso API según la política de seguridad institucional.

---

## 16. KPIs y Métricas

| KPI | Meta (Target) | Descripción |
| :--- | :--- | :--- |
| **Tasa de Éxito** | > 98% | Registros enviados sin errores en el primer intento. |
| **Tiempo de Procesamiento** | < 2 seg | Tiempo desde la acción en CMD hasta el registro en Firebase. |
| **Disponibilidad** | 99.9% | Tiempo de actividad del módulo de sincronización. |

---

## 17. Glosario

- **API REST**: Interfaz de programación que usa HTTP para intercambiar datos.
- **Payload**: El cuerpo de datos enviado en una petición JSON.
- **Webhook**: Mecanismo de notificación automática entre sistemas.
- **Firestore**: Base de datos de documentos de Firebase optimizada para tiempo real.

---

## 18. Anexos

### Ejemplo de Payload enviado desde CMD
```json
{
  "afiliado_id": "UUID-12345",
  "cedula": "402-XXXXXXX-X",
  "estado_id": 9,
  "updated_at": "2026-04-24T15:30:00Z"
}
```

### Ejemplo de Registro en Firebase (Monitoreo)
```json
{
  "id": "UUID-12345",
  "last_status": "Enviado",
  "sync_timestamp": "2026-04-24 15:30:05",
  "retry_count": 0
}
```
