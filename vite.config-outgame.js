import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    build: {
        minify: false,
        minifyIdentifiers: false,
        outDir: 'public',
        emptyOutDir: false, // we need to keep other files, but we manually delete just the needed ones above..
        rollupOptions: {
            treeshake: false,
            input: {
                outgame: path.resolve(__dirname, 'resources/js/outgame.js'),
            },
            output: {
                format: 'iife',
                entryFileNames: 'build/js/[name].js',
                assetFileNames: assetInfo => {
                    return 'build/js/[name][extname]';
                },
            },

        },
    },
});