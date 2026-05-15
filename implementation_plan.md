# Dashboard Ejecutivo Multi-App (Inteligencia Operativa)

Este plan detalla la arquitectura, el diseño y la estrategia de implementación para transformar el actual Dashboard Ejecutivo en una plataforma central de Inteligencia de Negocios (BI) que consolide la información de todo el ecosistema de ARS CMD.

## User Review Required

> [!IMPORTANT]
> **Aprobación de Arquitectura de Caché:** Para asegurar que el Dashboard sea extremadamente rápido (Performance), implementaremos un **Job en Background** (`WarmExecutiveDashboardCache`) que pre-calculará los KPIs complejos cada 15-30 minutos y los guardará en caché. El controlador solo leerá esta caché. ¿Está de acuerdo con este enfoque de "cálculo asíncrono" para garantizar tiempos de carga menores a 1 segundo?

> [!WARNING]
> **Librería de Gráficos:** Proponemos utilizar **ApexCharts** por su estética moderna (estilo Stripe/Linear), interactividad y soporte para modo oscuro. Si prefiere otra (como Chart.js o ECharts), por favor indíquelo.

## Open Questions

1. **Frecuencia de Actualización:** ¿Qué tan en "tiempo real" necesita los datos? ¿Es aceptable un retraso de 15 minutos a cambio de un rendimiento ultrarrápido?
2. **Exportaciones:** Menciona exportar el resumen en PDF y Excel. ¿Desea que la exportación sea de la vista actual filtrada o un reporte estándar predefinido?
3. **Filtros por defecto:** ¿Cuál debe ser la vista por defecto al entrar al dashboard? (Ej. "Este Mes", "Hoy", "Corte Activo").

---

## Proposed Changes

### 1. Capa de Inteligencia y Performance (Servicios y Jobs)

#### [NEW] `app/Services/ExecutiveDashboardService.php`
- Servicio central encargado de consultar y estructurar las métricas de todas las aplicaciones.
- Métodos separados por dominio: `getAfiliacionMetrics()`, `getTraspasosMetrics()`, `getCarnetizacionMetrics()`, `getDispersionMetrics()`, `getPssMetrics()`, `getCallCenterMetrics()`, `getAsistenciaMetrics()`, `getCloudMetrics()`.
- Lógica de comparación de períodos (Mes actual vs Mes anterior) para generar flechas de tendencia (Variación %).

#### [NEW] `app/Jobs/WarmExecutiveDashboardCache.php`
- Job programado en `Kernel.php` (ej. cada 15 minutos).
- Se encarga de llamar al `ExecutiveDashboardService` y guardar el resultado completo en `Cache::put('executive_dashboard_data', $data, 3600)`.
- Esto garantiza que el usuario experimente 0 lentitud al abrir el dashboard.

---

### 2. Controladores y Rutas

#### [MODIFY] `app/Http/Controllers/ExecutiveDashboardController.php`
- Se refactorizará para delegar todo el cálculo pesado al `ExecutiveDashboardService` (o leer directamente de la caché generada por el Job).
- Manejará los filtros por período (`?period=today`, `?period=month`, etc.) aplicando caché dinámica basada en el filtro.
- Validará los permisos granulares para decidir qué módulos (tabs) enviar a la vista.

#### [MODIFY] `routes/web.php`
- Actualizar o agregar rutas específicas para exportaciones (`/reportes/ejecutivo/export/pdf`, `/reportes/ejecutivo/export/excel`).

---

### 3. Vistas UI/UX Premium (Frontend)

Crearemos una nueva estructura de vistas bajo `resources/views/reportes/executive_v4/` para separar este diseño premium de los dashboards legacy.

#### [NEW] `resources/views/reportes/executive_v4/index.blade.php`
- Layout principal (Estilo Linear/Odoo).
- **Header:** Selector de períodos (Hoy, Semana, Mes, Trimestre, Año), botón de Exportar, Indicador de "Última actualización" (basado en la caché).
- **Navegación Interna (Tabs/Pills):** Resumen, Carnetización, Afiliación, Traspasos, Dispersión, PSS, Call Center, Asistencia, Cloud, Alertas.
- Diseño *responsive* con CSS Grid, *glassmorphism* sutil, *badges* de estado y *dark mode* compatible.

#### [NEW] Componentes Blade Reutilizables
- `components/executive/kpi-card.blade.php`: Tarjeta con valor principal, ícono, y porcentaje de variación (verde/rojo).
- `components/executive/chart-container.blade.php`: Contenedor estandarizado para ApexCharts.
- `components/executive/alert-badge.blade.php`: Para la sección de alertas inteligentes.

---

### 4. Detalles de las Métricas por Módulo

- **Resumen Ejecutivo:** Los 8 KPIs más importantes del ecosistema + Gráfico de radar o barras consolidado.
- **Carnetización (`Afiliado`):** Total, Completados, Pendientes Recepción, Críticos SLA, Costos por liquidar, Productividad por responsable.
- **Afiliación (`SolicitudAfiliacion`):** Recibidas, Aprobadas, Devueltas, Tiempo promedio de respuesta (SLA).
- **Traspasos (`Traspaso`):** Efectivos vs Rechazados, Hit Rate, Ranking de promotores.
- **Dispersión (`DispersionPeriod`):** Montos totales dispersados, cantidad de pensionados.
- **Prestadores (`PssCentro`, `PssMedico`):** Activos vs Inactivos, cobertura geográfica.
- **Call Center (`CallCenterRegistro`):** Tasa de conversión, efectividad de contacto.
- **Asistencia (`Asistencia\Registro`):** Ausentismo, tardanzas, personal activo vs presente hoy.
- **Alertas Inteligentes:** Lógica en el Service que detecta si un KPI sobrepasa un umbral (Ej. "Más de 50 afiliaciones devueltas hoy" -> Genera alerta roja).

---

## Verification Plan

### Automated Tests
- Validar mediante comandos Tinker que `ExecutiveDashboardService` retorna los arrays de datos en menos de 5 segundos sin caché.
- Ejecutar el Job `WarmExecutiveDashboardCache` y verificar que la llave de caché se crea correctamente.

### Manual Verification
- Ingresar al Dashboard Ejecutivo con un usuario Administrador y verificar que todos los tabs cargan instantáneamente (< 1s).
- Validar que los KPIs mostrados coincidan con los reportes individuales de cada módulo.
- Verificar el comportamiento *responsive* en simulación de pantallas de tablet y móvil.
- Probar el filtrado por "Hoy", "Mes Actual" y verificar que las métricas reaccionan adecuadamente.
- Probar exportación a PDF.
