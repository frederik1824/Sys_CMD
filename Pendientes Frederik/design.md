# 🎨 Design System: Centro de Comando (Command Center)

Este documento detalla la filosofía de diseño institucional y los componentes visuales del **Centro de Comando** de ARS CMD, evolucionado para proyectar profesionalismo, autoridad y eficiencia operativa.

---

## ✨ Filosofía Estética: "Institutional Command"
El diseño ha evolucionado de una estética puramente visual a una de **rendimiento y control empresarial**.

*   **Arquitectura de Navegación**: Implementación de un **Sidebar Fijo** que separa los controles globales del área de trabajo, creando una experiencia de plataforma tipo SaaS.
*   **Diseño Bento (Bento Grid)**: Organización de la información en contenedores modulares con bordes suavizados (`rounded-[48px]`), inspirada en interfaces modernas de alta gama (Apple/Stripe).
*   **Profundidad Ambiental**: Uso de gradientes radiales de baja saturación y sombras de proximidad para crear una jerarquía clara sin distracciones vibrantes.

---

## 🧱 Componentes Principales

### 1. HUD Operativo (Heads-Up Display)
*   **Métricas en Tiempo Real**: Panel superior con indicadores clave (KPIs) extraídos directamente de la base de datos:
    *   *Total de Solicitudes*: Volumen general.
    *   *Atención Urgente*: Contador crítico con animación de pulso.
    *   *Departamento*: Carga de trabajo específica del área del usuario.
    *   *Eficiencia Global*: Porcentaje de cumplimiento de SLAs.

### 2. Bento App Cards
*   **Contenido Dinámico**: Las tarjetas ahora incluyen una sección de datos operativos ("Bento Context") que muestra el estado y la prioridad actual del módulo.
*   **Identidad Visual Sobria**: Iconografía basada en **Phosphor Icons (Bold)** para un aspecto más técnico y uniforme.
*   **Glow de Proximidad**: Efectos de resplandor ambiental en el fondo que se activan solo al interactuar con la tarjeta.

### 3. Barra de Control Lateral
*   **Navegación de Alta Frecuencia**: Accesos directos a Apps, Analytics y Notificaciones mediante iconos estilizados sobre fondo `dark` (#0f172a).
*   **Gestión de Perfil**: Acceso integrado al perfil de usuario y cierre de sesión en la base de la barra.

---

## 🏗️ Stack Tecnológico Frontend

*   **Tipografía**: **Plus Jakarta Sans** (Fuente institucional de alta legibilidad y estilo moderno).
*   **Librería de Iconos**: [Phosphor Icons](https://phosphoricons.com) (Estilo Bold/Duotone para mayor peso visual).
*   **Layout**: Estructura de Flexbox con Sidebar persistente y área de contenido responsiva.
*   **Frameworks**: TailwindCSS (Estilizado) + Alpine.js (Interactividad y lógica de estado).

---

## 🚀 Reglas de UX Institucional
1.  **Enfoque en Datos**: Cada elemento visual debe servir para informar sobre el estado de la operación.
2.  **Transiciones Premium**: Las tarjetas utilizan `hover:-translate-y-3` con curvas de tiempo de 500ms para una sensación de suavidad y calidad.
3.  **Seguridad Visual**: El acceso restringido se indica mediante desaturación de la tarjeta y candados institucionales, manteniendo la estructura de la cuadrícula impecable.
