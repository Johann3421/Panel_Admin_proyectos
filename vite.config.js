import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/styles.css', // Incluye tu archivo CSS aquí
                'resources/js/script.js'      // Incluye otros archivos que necesites
            ],
            refresh: true,
        }),
    ],
});
