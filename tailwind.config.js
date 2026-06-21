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
            },
            colors: {
                primary: {
                    50: 'var(--primary-50, #eef2ff)',
                    100: 'var(--primary-100, #e0e7ff)',
                    200: 'var(--primary-200, #c7d2fe)',
                    300: 'var(--primary-300, #a5b4fc)',
                    400: 'var(--primary-400, #818cf8)',
                    500: 'var(--primary-500, #6366f1)',
                    600: 'var(--primary-600, #4f46e5)',
                    700: 'var(--primary-700, #4338ca)',
                    800: '#3730a3',
                    900: '#312e81',
                },
                text: {
                    primary: 'var(--text-primary, #0f172a)',
                    secondary: 'var(--text-secondary, #64748b)',
                    muted: 'var(--text-muted, #94a3b8)',
                },
                bg: {
                    primary: 'var(--bg-primary, #ffffff)',
                    secondary: 'var(--bg-secondary, #f8fafc)',
                },
                surface: {
                    secondary: 'var(--bg-secondary, #f8fafc)',
                },
                border: 'var(--border, #e2e8f0)',
                danger: 'var(--danger, #ef4444)',
                success: 'var(--success, #10b981)',
                warning: 'var(--warning, #f59e0b)',
            }
        },
    },

    plugins: [forms],
};
