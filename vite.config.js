import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/landing.css',
                'resources/css/dashboard.css',
                'resources/css/settings.css',
                'resources/css/categories.css',
                'resources/css/products.css',
                'resources/css/admin-layout.css',
                'resources/css/app-layout.css',
                'resources/css/auth.css',
                'resources/css/orders.css',
                'resources/css/catalog.css',
                'resources/css/product-detail.css',
                'resources/css/cart.css',
                'resources/css/checkout.css',
                'resources/css/customers.css'
            ],
            refresh: true,
        }),
    ],
});
