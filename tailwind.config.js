import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js', // Added for JS files
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'touristay-red': '#FF5733',
                'touristay-green': '#00C4B4',
                'touristay-dark': '#1A1A1A',
                'touristay-white': '#FFFFFF',
            },
        },
    },

    plugins: [forms],
}