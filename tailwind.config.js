import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                headline: ["Space Grotesk"],
                body: ["Inter"],
                label: ["Inter"],
                mono: ["JetBrains Mono"],
            },
            colors: {
                "surface-container-lowest": "#0a0e14",
                "on-surface-variant": "#bac9cc",
                "surface-container-highest": "#31353c",
                "on-background": "#dfe2eb",
                "tertiary-fixed": "#69ff87",
                "on-surface": "#dfe2eb",
                "on-secondary-fixed": "#20005f",
                "on-secondary": "#370096",
                "on-secondary-fixed-variant": "#4f00d0",
                "on-primary-container": "#00626e",
                "error-container": "#93000a",
                "surface-container": "#1c2026",
                "on-secondary-container": "#c0acff",
                "tertiary-fixed-dim": "#3ce36a",
                "surface": "#10141a",
                "primary": "#c3f5ff",
                "secondary-fixed-dim": "#cdbdff",
                "outline": "#849396",
                "on-error-container": "#ffdad6",
                "primary-fixed-dim": "#00daf3",
                "on-tertiary-container": "#006727",
                "on-tertiary": "#003912",
                "outline-variant": "#3b494c",
                "inverse-primary": "#006875",
                "surface-dim": "#10141a",
                "on-error": "#690005",
                "on-tertiary-fixed-variant": "#00531e",
                "on-primary-fixed-variant": "#004f58",
                "tertiary": "#b1ffb5",
                "primary-container": "#00e5ff",
                "on-primary": "#00363d",
                "surface-bright": "#353940",
                "background": "#10141a",
                "surface-variant": "#31353c",
                "inverse-surface": "#dfe2eb",
                "primary-fixed": "#9cf0ff",
                "on-tertiary-fixed": "#002108",
                "secondary-container": "#5203d5",
                "surface-container-high": "#262a31",
                "tertiary-container": "#49ed72",
                "surface-tint": "#00daf3",
                "surface-container-low": "#181c22",
                "inverse-on-surface": "#2d3137",
                "secondary-fixed": "#e8deff",
                "secondary": "#cdbdff",
                "error": "#ffb4ab",
                "on-primary-fixed": "#001f24"
            }
        },
    },

    plugins: [forms],
};
