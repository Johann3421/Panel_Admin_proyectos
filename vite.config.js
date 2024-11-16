import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/styles.css', 'resources/css/style.css','resources/css/styles1.css',
                'resources/js/app.js', 'resources/js/script.js'
            ],
            refresh: true,
        }),
    ],
    
});
