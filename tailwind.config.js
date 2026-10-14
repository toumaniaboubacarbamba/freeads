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
            sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
        },
        colors: {
            'primary': '#954a00',
            'primary-container': '#f77f00',
            'secondary': '#006d40',
            'secondary-container': '#7bf7b0',
            'secondary-fixed-dim': '#61dd98',
            'on-secondary-container': '#007244',
            'surface': '#fff8f5',
            'surface-container': '#ffeadf',
            'surface-container-low': '#fff1ea',
            'surface-container-lowest': '#ffffff',
            'outline': '#8a7263',
            'outline-variant': '#dec1af',
            'on-surface': '#251911',
            'on-surface-variant': '#574335',
            'inverse-surface': '#3c2d25',
            // anciens noms, pour les pages pas encore converties
            ci: { orange: '#F77F00', green: '#009E60', cream: '#FFFAF3', brown: '#2B1A0E' },
        },
    },
},

    plugins: [forms],
};
