# 🗺️ Documentación: Mapa Global y Geocodificación Estática

Esta sección detalla las mejoras realizadas para la gestión de ubicaciones de empresas y la visualización en el mapa de calor corporativo.

## 1. Geocodificación Automática via URL
Se ha implementado una lógica inteligente en el modelo `Empresa` para extraer coordenadas geográficas sin intervención manual.

### 🔌 Soporte de Enlaces
El sistema es capaz de procesar dos tipos de fuentes:
*   **Enlaces Largos**: `https://www.google.com/maps/place/18.456,-69.987/...` (Extrae coordenadas directamente del string).
*   **Enlaces Cortos (Resolución HTTP)**: `https://goo.gl/maps/xyz...`. El sistema realiza una petición `HEAD` silenciosa para obtener la URL final y luego procesarla.

### ⚙️ Funcionamiento (Model Empresa)
Se configuró un **Attribute Setter** para el campo `google_maps_url`. Al pegar un enlace:
1.  Si es un enlace acortado, el servidor lo resuelve.
2.  Usa una expresión regular (Regex) para buscar el patrón `@lat,long`.
3.  Auto-llena los campos `latitud` y `longitud` de la base de datos automáticamente.

---

## 2. Mapa Global (Heatmap)
El mapa de calor corporativo ha sido optimizado para mayor estabilidad y navegación.

### 📍 Marcadores por RNC
*   **Navegación Robusta**: Los marcadores del mapa ya no usan el ID numérico interno (que puede cambiar entre instalaciones). Ahora los enlaces de "Ver Ficha" en el mapa usan el **RNC** de la empresa.
*   **URL Generada**: `http://dominio.com/empresas/{RNC}`.
*   **Fallback**: Si una empresa no tiene RNC, el sistema usa su **UUID**, garantizando que el mapa nunca genere un enlace roto.

### 📊 Lógica de Calor
*   **Agrupación**: El mapa agrupa las empresas por proximidad y utiliza un gradiente de color para indicar la densidad de clientes en zonas específicas.
*   **Filtros**: Se puede filtrar la visualización por Provincia o Municipio, permitiendo un análisis de cobertura más preciso.

---

## 3. Instrucciones para el Usuario
Para agregar una ubicación ahora, el usuario solo debe:
1.  Ir a Google Maps.
2.  Copiar el enlace de compartir de la empresa.
3.  Pegarlo en el campo **"Enlace Google Maps"** en el formulario de edición de la empresa.
4.  **Guardar**: Las coordenadas se calculan solas al momento de guardar el registro.

---
*Documento preparado por el equipo técnico de CMD.*
