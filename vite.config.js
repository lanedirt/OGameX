import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/ingame.css',
                'resources/css/outgame.css',
                'resources/js/ingame.js',
                'resources/js/outgame.js'
            ],
            refresh : true
        }),
    ],
});
