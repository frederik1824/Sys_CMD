---
name: Crystal Clear Command
colors:
  surface: '#f3fbfc'
  surface-dim: '#d4dbdd'
  surface-bright: '#f3fbfc'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eef5f6'
  surface-container: '#e8eff1'
  surface-container-high: '#e2e9eb'
  surface-container-highest: '#dce4e5'
  on-surface: '#161d1e'
  on-surface-variant: '#3b494c'
  inverse-surface: '#2a3233'
  inverse-on-surface: '#ebf2f4'
  outline: '#6b7a7d'
  outline-variant: '#bac9cc'
  surface-tint: '#006875'
  primary: '#006875'
  on-primary: '#ffffff'
  primary-container: '#00daf3'
  on-primary-container: '#005b67'
  inverse-primary: '#01daf3'
  secondary: '#006c49'
  on-secondary: '#ffffff'
  secondary-container: '#6cf8bb'
  on-secondary-container: '#00714d'
  tertiary: '#bc0b3b'
  on-tertiary: '#ffffff'
  tertiary-container: '#ffb2b6'
  on-tertiary-container: '#a70031'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9cefff'
  primary-fixed-dim: '#01daf3'
  on-primary-fixed: '#001f24'
  on-primary-fixed-variant: '#004f58'
  secondary-fixed: '#6ffbbe'
  secondary-fixed-dim: '#4edea3'
  on-secondary-fixed: '#002113'
  on-secondary-fixed-variant: '#005236'
  tertiary-fixed: '#ffdadb'
  tertiary-fixed-dim: '#ffb2b7'
  on-tertiary-fixed: '#40000d'
  on-tertiary-fixed-variant: '#92002a'
  background: '#f3fbfc'
  on-background: '#161d1e'
  surface-variant: '#dce4e5'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  body-base:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  terminal-code:
    fontFamily: Space Grotesk
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.5'
    letterSpacing: 0.05em
  label-caps:
    fontFamily: Space Grotesk
    fontSize: 12px
    fontWeight: '700'
    lineHeight: '1'
    letterSpacing: 0.1em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 48px
  gutter: 24px
  margin-page: 40px
---

## Brand & Style
The design system is engineered to evoke the feeling of a high-end, futuristic mission control. It prioritizes clarity, precision, and an expansive sense of space. The brand personality is "Technical Elegance"—sophisticated enough for executive overviews, yet precise enough for deep-data operations. 

The aesthetic is rooted in **Glassmorphism**, utilizing extreme transparency and "frosted" optical effects to create a sense of depth without weight. It moves away from heavy, opaque surfaces in favor of light-refracting layers, making the interface feel like a precision-milled glass instrument. The target audience includes high-level analysts and technical leads who require a "heads-up display" (HUD) experience that is both professional and airy.

## Colors
The palette is dominated by "Optical White" and "Arctic Gray" to maintain a high-transparency feel. The core interaction color is **Vibrant Cyan**, chosen for its high visibility and association with advanced technology.

- **Primary (Cyan):** Used for primary actions, active states, and data highlights.
- **Success (Emerald):** Reserved for positive health indicators and completed processes.
- **Warning (Soft Rose):** Used for critical alerts and destructive actions.
- **Neutral Stack:** Uses a progression from pure white (#FFFFFF) to a cool-toned slate (#F8FAFC) to create subtle contrast between background and container layers.

## Typography
The typographic strategy balances human-centric readability with technical utility. 

- **Inter** serves as the workhorse for headlines and body copy, ensuring maximum legibility across all densities. 
- **Space Grotesk** is introduced for "Terminal Elements"—data labels, status codes, and ID tags. This monospace-inspired font reinforces the "Command Center" aesthetic. 

Small caps and increased letter spacing should be applied to all label-level elements to maintain an organized, tabulated appearance typical of professional instrument panels.

## Layout & Spacing
The layout follows a **Fluid Grid** philosophy to maximize the "Command Center" feel, allowing data visualization to expand across ultra-wide displays. 

- **Grid:** A 12-column grid with generous 24px gutters.
- **Rhythm:** An 8px linear scale for internal component spacing, with a 4px "micro-step" for tight data sets.
- **Density:** High density is preferred for data-heavy views, but individual panels should maintain large internal padding (24px+) to prevent the glass surfaces from feeling cluttered.
- **Margins:** Wide page margins (40px+) create a "floating" effect for the primary interface containers.

## Elevation & Depth
Depth is the most critical element of this design system. It is achieved through "Atmospheric Layering" rather than traditional heavy shadows.

- **The Base:** Pure White (#FFFFFF).
- **The Glass Layer:** Surfaces use `backdrop-filter: blur(20px)` and a white-tinted transparency (`rgba(255, 255, 255, 0.4)`).
- **The Outline:** Every glass panel must have a 1px solid border in a high-opacity white (`rgba(255, 255, 255, 0.8)`), acting as a "rim light" that defines the edge against the background.
- **Shadows:** Use multi-layered, low-opacity shadows. A typical shadow stack involves a sharp, close-in shadow for definition and a very wide, soft, pale-blue tinted shadow to simulate light passing through glass.

## Shapes
Shapes are "Softened Geometric." While the grid is rigid and professional, the corners are rounded to maintain the premium, friendly feel of modern hardware. 

- **Containers:** Use `rounded-lg` (1rem/16px) for main dashboard panels and cards.
- **Interactions:** Use `0.5rem` (8px) for buttons and input fields.
- **Selection Indicators:** Small indicators (like active tab markers) should be pill-shaped to stand out against the geometric background.

## Components
- **Glass Cards:** The primary container. Must feature the backdrop blur, white border, and soft shadow stack. Headlines inside cards should always be paired with a Space Grotesk "Category Label" above them.
- **Ghost Buttons:** Secondary actions should use a subtle white fill that brightens on hover.
- **Primary Action (Cyan):** Buttons should use a solid Vibrant Cyan fill with white text. Apply a subtle "glow" (shadow with cyan tint) on hover.
- **Data Terminal:** A specialized component for log data or system outputs. Uses a #F8FAFC background, Space Grotesk font, and Emerald Green text highlights for "Live" status.
- **Status Chips:** Use high-saturation backgrounds (Cyan, Emerald, Rose) with 10% opacity, paired with full-saturation text for a "light-up" effect.
- **Segmented Control:** A glass-based toggle where the active state is a solid white "pill" that appears to float above the blurred background.