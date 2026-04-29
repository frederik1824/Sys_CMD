# Cómo usar esta Plantilla en otros Sistemas

Para replicar esta línea gráfica en un nuevo proyecto Laravel, siga estos pasos:

## 1. Copiar Componentes
Copie las siguientes carpetas a su nuevo proyecto:
- `resources/views/components/layout/` -> Estructura base.
- `resources/views/components/ui/` -> Elementos visuales (Cards, Badges, Stats).

## 2. Configurar el Layout Base
En su `app.blade.php`, utilice los componentes de la siguiente manera:

```blade
<div class="min-h-screen bg-slate-50 font-sans antialiased flex">
    <!-- Barra Lateral -->
    <x-layout.sidebar>
        <a href="/" class="flex items-center gap-3 px-4 py-3 text-blue-900 font-semibold border-l-4 border-blue-900 bg-white/50">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-[0.6875rem] uppercase font-medium">Dashboard</span>
        </a>
        <!-- Más links aquí... -->
    </x-layout.sidebar>

    <main class="flex-1 pl-64">
        <!-- Barra Superior -->
        <x-layout.topbar>
            <x-slot name="search">
                <x-layout.search-palette />
            </x-slot>
            
            <!-- Iconos de Acción (Notificaciones, Perfil) -->
            <button class="...">...</button>
        </x-layout.topbar>

        <!-- Contenido -->
        <div class="p-10 max-w-[1600px] mx-auto">
            {{ $slot }}
        </div>
    </main>
</div>
```

## 3. Dependencias Visuales
Asegúrese de incluir estas librerías en su `<head>`:
- **Fuentes**: `Inter` u `Outfit` desde Google Fonts.
- **Iconos**: `Material Symbols Outlined`.
- **CSS**: Tailwind CSS configurado (v3+).

## 4. Estándares de Diseño
Consulte `docs/design_system.md` para conocer los códigos de colores, espaciados y reglas de sombras para mantener la coherencia.
