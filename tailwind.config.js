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
            colors: {
                // Couleurs du theme restaurant
                primary: {
                    50: '#faf6f3',
                    100: '#f2ebe4',
                    200: '#e5d5c7',
                    300: '#d4b9a3',
                    400: '#c49d7f',
                    500: '#B08968', // Couleur principale or/bronze
                    600: '#9a7355',
                    700: '#7d5c45',
                    800: '#674c3a',
                    900: '#564032',
                },
                anthracite: '#2E2E2E',
                // Statuts des tables
                'statut-disponible': '#4CAF50',
                'statut-reservee': '#FF9800',
                'statut-occupee': '#F44336',
            },
            fontFamily: {
                // Titres elegants
                display: ['Playfair Display', ...defaultTheme.fontFamily.serif],
                // Textes lisibles
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
