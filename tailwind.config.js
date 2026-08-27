import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Off-black / off-white app-chrome palette. Kept separate from
                // Tailwind's `black`/`white` so the resume template (Shared/ResumeTemplate.vue)
                // stays pure black-on-white for ATS/print fidelity with the resume-gen reference.
                ink: '#0a0a0a',
                paper: '#fafafa',
                charcoal: '#171717',
            },
        },
    },

    plugins: [forms],
};
