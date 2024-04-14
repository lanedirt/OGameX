import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/ingame.css', 'resources/css/ingame.css', 'resources/js/ingame.js'],
            refresh: true,
        }),
    ],
});