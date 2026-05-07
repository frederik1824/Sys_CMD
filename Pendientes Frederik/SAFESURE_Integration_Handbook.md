# 📋 ARS CMD — SysCarnet: Manual de Integración para SAFESURE
> **Versión del Documento:** 2.0 — Abril 2026  
> **Audiencia:** Equipo técnico de SAFESURE  
> **Propósito:** Guía de integración y flujo de datos entre el sistema de carnetización de ARS CMD (SysCarnet) y la plataforma SAFESURE para sincronización bidireccional vía Google Firebase Firestore.

---

## 1. 🏛️ Visión General de la Arquitectura

El ecosistema está compuesto por **dos sistemas independientes** que comparten una capa de datos común en la nube:

```
┌─────────────────────┐       Google Firebase        ┌──────────────────────┐
│      ARS CMD        │      (Firestore REST API)    │      SAFESURE        │
│    "SysCarnet"      │ ◄──────────────────────────► │   (Tu Plataforma)    │
│  Laravel 11 / MySQL │                              │                      │
└─────────────────────┘                              └──────────────────────┘
        │                                                       │
        └──────────── Colecciones Compartidas ──────────────────┘
                    • afiliados
                    • empresas
```

**Rol de cada sistema:**
| Sistema | Variable ENV | Responsabilidad Principal |
|---|---|---|
| ARS CMD (SysCarnet) | `FIREBASE_SYNC_ROLE=CMD` | Carnetización, logística de entrega, gestión de estados |
| SAFESURE | `FIREBASE_SYNC_ROLE=SAFE` | Gestión del trámite de seguro, confirmación de entrega |

---

## 2. 🔑 Configuración de Acceso a Firebase

### Variables de Entorno Requeridas
```env
FIREBASE_PROJECT_ID=syscarnet
FIREBASE_CREDENTIALS=firebase-auth.json   # Ruta al Service Account JSON
FIREBASE_SYNC_ROLE=SAFE                   # Identificador de tu sistema
```

### Autenticación
El sistema CMD utiliza autenticación **OAuth2 con JWT RS256** directamente contra la API REST de Google, sin depender del SDK de Firebase o la extensión gRPC. SAFESURE puede implementar el mismo mecanismo:

1. Descarga el Service Account JSON del proyecto `syscarnet` en Firebase Console.
2. Genera un JWT firmado con la clave privada del Service Account.
3. Intercambia el JWT por un Access Token en `https://oauth2.googleapis.com/token`.
4. Usa el Access Token como `Bearer` en todas las llamadas a la API REST de Firestore.
5. **El token es válido por 1 hora.** Se recomienda cachearlo para evitar llamadas repetidas de autenticación.

**Endpoint base de la API REST:**
```
https://firestore.googleapis.com/v1/projects/syscarnet/databases/(default)/documents
```

---

## 3. 📦 Colecciones de Firebase (Contrato de Datos)

### 3.1 Colección: `afiliados`

**Document ID:** `cedula` del afiliado (formato `000-0000000-0`).

> ⚠️ **Importante:** La cédula **siempre debe estar formateada** con guiones. CMD rechazará/ignorará documentos sin el formato correcto.

| Campo Firebase | Tipo | Descripción |
|---|---|---|
| `uuid` | string | Identificador único UUID (inmutable) |
| `nombre_completo` | string | Nombre en Title Case (normalizado automáticamente) |
| `cedula` | string | Cédula formateada `000-0000000-0` |
| `sexo` | string | `M` o `F` |
| `telefono` | string | Teléfono principal |
| `direccion` | string | Dirección física |
| `provincia` | string | Nombre de la provincia (texto) |
| `municipio` | string | Nombre del municipio (texto) |
| `empresa` | string | Nombre de la empresa empleadora |
| `rnc_empresa` | string | RNC de la empresa (solo dígitos) |
| `poliza` | string | Número de póliza del seguro |
| `contrato` | string | Número de contrato |
| `estado_id` | integer | **Ver Tabla de Estados** abajo |
| `empresa_id` | integer | ID de la empresa en el sistema CMD |
| `responsable_id` | integer | ID del responsable de zona CMD |
| `corte_id` | integer | ID del período de procesamiento |
| `lote_id` | integer | ID del lote de importación |
| `fecha_entrega_proveedor` | string | ISO 8601. Fecha en que CMD envió al proveedor de carnets |
| `fecha_entrega_safesure` | string | ISO 8601. **Fecha en que SAFESURE confirma la entrega** |
| `costo_entrega` | float | Costo de producción del carnet |
| `liquidado` | boolean | `true` si el trámite fue liquidado financieramente |
| `fecha_liquidacion` | string | ISO 8601. Fecha de liquidación |
| `firebase_synced_at` | string | ISO 8601. Timestamp del último push exitoso |
| `updated_at` | string | ISO 8601. **Campo clave para sincronización incremental** |

### 3.2 Colección: `empresas`

**Document ID:** `uuid` de la empresa (UUID v4).

| Campo Firebase | Tipo | Descripción |
|---|---|---|
| `uuid` | string | Identificador único UUID (Document ID) |
| `nombre` | string | Nombre legal de la empresa |
| `rnc` | string | RNC de la empresa (solo dígitos) |
| `direccion` | string | Dirección fiscal |
| `telefono` | string | Teléfono corporativo |
| `es_verificada` | boolean | `true` si CMD ha verificado la empresa |
| `es_real` | boolean | `true` si es empresa real (no filial ficticia) |
| `es_filial` | boolean | `true` si es una filial |
| `provincia_id` | integer | ID de provincia |
| `municipio_id` | integer | ID de municipio |
| `contacto_nombre` | string | Nombre del contacto clave |
| `contacto_puesto` | string | Cargo del contacto |
| `contacto_telefono` | string | Teléfono directo del contacto |
| `contacto_email` | string | Email del contacto |
| `latitude` | float | Coordenada geográfica (extraída automáticamente de Google Maps) |
| `longitude` | float | Coordenada geográfica |
| `estado_contacto` | string | Estado CRM: `Nuevo`, `En contacto`, `Activo`, `Inactivo` |

---

## 4. 🚦 Tabla de Estados de Afiliados (`estado_id`)

Esta es la tabla de referencia del flujo de estados. **Ambos sistemas deben respetar el orden y la propiedad de cada estado:**

| ID | Nombre | Propietario | Descripción |
|---|---|---|---|
| 1 | Pendiente | CMD | Afiliado importado, en cola de procesamiento |
| 2 | En Producción | CMD | Carnet en proceso de fabricación |
| 3 | En Ruta | CMD | Carnet despachado con mensajero |
| 4 | No Localizado | CMD | Fallo en entrega logística |
| 5 | Rechazado | CMD | Trámite rechazado internamente |
| 6 | Entregado | CMD | Carnet entregado físicamente |
| 7 | Pendiente de Recepción | CMD | CMD aguarda confirmación de SAFESURE |
| 8 | En Revisión | CMD | Caso bajo revisión administrativa |
| **9** | **Completado** | **SAFESURE** | **SAFESURE confirma la entrega o cierre del trámite** |
| 11 | Prospecto | CMD/CallCenter | Afiliado sin cédula, en etapa de gestión de datos |
| 12 | Cédula Pendiente | CallCenter | Cédula efectiva, documento no recibido aún |
| 13 | Cédula Recibida | CallCenter | Documento físico recibido, listo para carnetización |

### ⚠️ Regla de Negocio Crítica (Gating Rule)

Cuando **SAFESURE** escribe `estado_id = 9` (Completado) en Firebase:

- Si el afiliado **ya está** en estado 9 en CMD → CMD respeta y acepta el cambio.
- Si el afiliado **aún no está** en estado 9 en CMD → CMD lo mapea automáticamente a **estado 7** (Pendiente de Recepción) hasta confirmar el proceso localmente.

> Esta regla evita que un push masivo de SAFESURE sobrescriba el flujo logístico de CMD antes de su confirmación interna.

---

## 5. 🔄 Flujo de Sincronización Bidireccional

### 5.1 Push (SAFESURE → Firebase)
SAFESURE debe hacer **PATCH** al documento del afiliado cuando:
- Se confirma la entrega del carnet → Escribir `estado_id = 9` y `fecha_entrega_safesure`.
- Se actualiza información de contacto del afiliado.

**Endpoint:**
```
PATCH https://firestore.googleapis.com/v1/projects/syscarnet/databases/(default)/documents/afiliados/{CEDULA_FORMATEADA}
Authorization: Bearer {ACCESS_TOKEN}
Content-Type: application/json

{
  "fields": {
    "estado_id": { "integerValue": "9" },
    "fecha_entrega_safesure": { "stringValue": "2026-04-29T14:30:00Z" }
  }
}
```

### 5.2 Pull (Firebase → SAFESURE)
SAFESURE debe consultar Firebase para obtener actualizaciones de CMD (nuevos carnets despachados, cambios de estado, etc.).

**Consulta Incremental (Recomendada):** Consultar solo documentos modificados desde la última sincronización usando el campo `updated_at`:
```json
POST /documents:runQuery
{
  "structuredQuery": {
    "from": [{ "collectionId": "afiliados" }],
    "where": {
      "fieldFilter": {
        "field": { "fieldPath": "updated_at" },
        "op": "GREATER_THAN",
        "value": { "stringValue": "2026-04-29T00:00:00Z" }
      }
    }
  }
}
```

> **Recomendación:** CMD usa este patrón internamente para evitar el error 429 (cuota excedida). Se recomienda que SAFESURE adopte el mismo patrón incremental en lugar de sincronizaciones completas (full scan).

---

## 6. 🖥️ Centro de Sincronización CMD — Referencia para Mejora en SAFESURE

ARS CMD opera un **Sync Center** integrado en la interfaz de administración. Compartimos su arquitectura para que SAFESURE pueda implementar algo equivalente.

### 6.1 ¿Qué es el Sync Center?
Es un dashboard de comando y control de la sincronización con Firebase. Provee:

- **Monitor de Cuotas en Tiempo Real:** Contador de lecturas y escrituras en las últimas 24 horas (límite gratuito: 50,000 lecturas / 20,000 escrituras).
- **Terminal de Sincronización en Vivo:** Feed de logs en tiempo real de cada operación Push/Pull, con timestamps y estados codificados por color.
- **Control de Intensidad:** Modos operativos para controlar la velocidad de sincronización:
  - **Normal:** 1 segundo de pausa entre operaciones (recomendado para uso diario).
  - **Turbo:** 500ms de pausa (para sincronizaciones nocturnas).
  - **Overdrive:** Sin pausa (solo para sincronizaciones de emergencia).
- **Análisis Delta:** Comparación en tiempo real entre el conteo local (MySQL) vs. el conteo en Firebase para detectar discrepancias.
- **Sincronización Incremental:** Motor que consulta solo registros modificados desde la última sincronización exitosa, evitando el error 429.
- **Panel de Historial:** Logs paginados via AJAX de todas las operaciones con filtros por entidad, estado y rango de fechas.

### 6.2 Arquitectura Técnica del Sync Center CMD

```
┌─────────────────────────────────┐
│        Sync Center UI           │
│  (Blade + AlpineJS + Fetch API) │
└────────────┬────────────────────┘
             │ AJAX polling (cada 5s)
             ▼
┌─────────────────────────────────┐
│  FirebaseSyncController         │
│  GET /sistema/firebase/feed     │  ← Obtiene el feed de logs del caché
│  POST /sistema/firebase/sync    │  ← Dispara un Job de sincronización
│  GET /sistema/firebase/status   │  ← Estado del Job actual (Running/Done)
└────────────┬────────────────────┘
             │ Dispatch
             ▼
┌─────────────────────────────────┐
│  FirebaseSyncJob (Queue Worker) │
│  - Chunk de 50 registros/vez    │
│  - Pausas controladas           │
│  - Manejo de error 429          │
│  - Progreso en tiempo real      │
└────────────┬────────────────────┘
             │ API REST
             ▼
┌─────────────────────────────────┐
│     Google Firebase Firestore   │
│  Proyecto: syscarnet            │
└─────────────────────────────────┘
```

### 6.3 Recomendaciones para el Sync Center de SAFESURE

Basándonos en los problemas que hemos encontrado, recomendamos las siguientes mejoras para el sistema de SAFESURE:

1. **Implementar Sincronización Incremental:** Es el cambio más importante. Nunca hacer un full-scan de toda la colección si se puede filtrar por `updated_at`. Esto reduce el consumo de cuota en un ~95%.

2. **Caché de Access Token:** El token de autenticación es válido 1 hora. Almacenarlo en caché evita una llamada HTTP de autenticación por cada operación.

3. **Procesamiento en Chunks:** Nunca sincronizar más de 500 documentos en una sola llamada (límite de Firestore para batch commits). Procesar en lotes de 50-100 con pausas de 500ms entre ellos.

4. **Manejo del Error 429:** Implementar un mecanismo de retry con backoff exponencial al recibir errores de cuota:
   ```
   Intento 1: Pausa 2s
   Intento 2: Pausa 4s
   Intento 3: Pausa 8s
   → Si sigue fallando, pausar la sincronización y notificar al operador.
   ```

5. **Feed de Logs en Cache:** Almacenar los últimos 50 eventos de sincronización en Redis/Cache para mostrar el terminal en vivo sin consultar logs de disco.

6. **Monitor de Discrepancias:** Implementar un endpoint que compare `COUNT(*)` de tu base de datos local vs. `runAggregationQuery` de Firebase para detectar documentos fuera de sync.

---

## 7. 🔐 Reglas de Seguridad y Buenas Prácticas

- **Nunca** exponer el `firebase-auth.json` (Service Account) en el repositorio de código.
- **Nunca** hacer writes masivos durante horas pico (9am-6pm) para conservar cuota.
- **Siempre** usar el campo `firebase_synced_at` para auditar la última sincronización exitosa de cada documento.
- **Siempre** validar el formato de la cédula antes de usar el Document ID: `000-0000000-0`.
- Mantener **solo los campos necesarios** en cada push para reducir el consumo de escritura.

---

## 8. 📞 Campos que SAFESURE Debe Escribir (Responsabilidad)

Para mantener la integridad del flujo, SAFESURE solo debe escribir en Firebase los campos de su dominio:

| Campo | Acción SAFESURE | Descripción |
|---|---|---|
| `estado_id` | Write `= 9` | Confirmar que el trámite está completado |
| `fecha_entrega_safesure` | Write `= ISO datetime` | Registrar la fecha exacta de entrega/cierre |
| `liquidado` | Write `= true/false` | Marcar si el trámite fue liquidado |
| `fecha_liquidacion` | Write `= ISO datetime` | Fecha de liquidación |
| `recibo_liquidacion` | Write `= string` | Número de recibo de liquidación |

> Cualquier otro campo no listado es de responsabilidad de CMD. Modificarlos sin coordinación puede romper el flujo de carnetización.

---

## 9. 🧪 Verificar la Conexión

Para verificar que tu Service Account tiene acceso al proyecto, puedes usar este request de prueba:

```bash
# Obtener un document de prueba (reemplaza CEDULA con una cédula real)
curl -X GET \
  "https://firestore.googleapis.com/v1/projects/syscarnet/databases/(default)/documents/afiliados/001-1234567-8" \
  -H "Authorization: Bearer {TU_ACCESS_TOKEN}"
```

Una respuesta exitosa tendrá `statusCode: 200` y el documento con los campos tipados de Firestore.

---

## 10. 📋 Resumen de Endpoints Firestore REST Utilizados

| Operación | Método | URL |
|---|---|---|
| Obtener un documento | GET | `/documents/{collection}/{docId}` |
| Crear/Actualizar (merge) | PATCH | `/documents/{collection}/{docId}` |
| Batch Write (hasta 500) | POST | `/documents:commit` |
| Query estructurada | POST | `/documents:runQuery` |
| Count agregado | POST | `/documents:runAggregationQuery` |
| Listar colección (paginado) | GET | `/documents/{collection}?pageToken=...` |
| Eliminar documento | DELETE | `/documents/{collection}/{docId}` |

---

*Documento generado por el equipo técnico de ARS CMD — SysCarnet v2.0 — Abril 2026.*  
*Para consultas técnicas sobre integración, coordinar con el equipo de sistemas de ARS CMD.*
