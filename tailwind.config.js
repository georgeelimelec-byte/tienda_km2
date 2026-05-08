import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './Modules/**/resources/**/*.blade.php',
        './Modules/**/resources/**/*.js',
        './Modules/**/resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    DEFAULT: '#f97316', // Orange 500
                    light: '#fb923c',   // Orange 400
                    dark: '#ea580c',    // Orange 600
                }
            }
        },
    },
    plugins: [],
};
