# Enterprise Core Design System

Este documento define la línea gráfica estándar para los sistemas de la organización, asegurando consistencia visual y una experiencia de usuario premium.

## 1. Paleta de Colores
Utilizamos una base de **Slate** para la estructura y **Primary Blue** para acciones e identidad.

| Color | Token | Uso |
| :--- | :--- | :--- |
| **Primary** | `#004a99` | Acciones principales, branding, botones. |
| **Surface** | `#f8fafc` | Fondos de página y barra lateral (Slate 50). |
| **Border** | `#f1f5f9` | Divisiones suaves y bordes de cards (Slate 100). |
| **Text Main** | `#0f172a` | Encabezados y texto importante (Slate 900). |
| **Text Muted** | `#64748b` | Descripciones y etiquetas secundarias (Slate 500). |

## 2. Tipografía
- **Fuente Principal**: [Inter](https://fonts.google.com/specimen/Inter) o [Outfit](https://fonts.google.com/specimen/Outfit).
- **Estilo**: Pesos `900 (Black)` para títulos y `400/500` para cuerpo.
- **Títulos**: Uso de `tracking-tighter` para un look moderno y compacto.

## 3. Componentes de Identidad
### Cards
- **Radio de Borde**: `rounded-[2.5rem]` (Extra Rounded).
- **Sombras**: `shadow-sm` base, `shadow-xl` en hover.
- **Padding**: Generoso (`p-8`).

### Iconografía
- **Librería**: [Google Material Symbols Outlined](https://fonts.google.com/icons?icon.set=Material+Symbols).
- **Contenedores**: Iconos dentro de cuadrados con bordes redondeados (`rounded-2xl`) y fondos tenues (`bg-primary/10`).

## 4. Estructura de Layout (Blade Components)
Para mantener la línea, cada sistema debe usar:
- `<x-layout.sidebar>`: Menú lateral con branding.
- `<x-layout.topbar>`: Barra superior con búsqueda y perfil.
- `<x-layout.search-palette>`: Buscador inteligente (Ctrl+K).
- `<x-ui.card>`: Contenedor estándar de información.
- `<x-ui.stats-card>`: Para visualización de métricas (KPIs).

## 5. Micro-interacciones
- **Hover Effects**: Siempre usar `transition-all duration-300`.
- **Escalado**: `group-hover:scale-110` en iconos para dar sensación de vida.
- **Brillo**: Fondos con `backdrop-blur-md` en el TopBar.
