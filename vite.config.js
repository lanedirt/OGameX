import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig({
    build: {
        rollupOptions: {
            input: {
                ingame: path.resolve(__dirname, 'resources/js/ingame.js'),
                outgame: path.resolve(__dirname, 'resources/js/outgame.js'),
                ingameStyle: path.resolve(__dirname, 'resources/css/ingame.css'),
                outgameStyle: path.resolve(__dirname, 'resources/css/outgame.css'),
            },
            output: {
                inlineDynamicImports: false,
                format: 'commonjs',
                entryFileNames: 'build/js/[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        return 'build/css/[name][extname]';
                    }
                    return 'build/assets/[name][extname]';
                },
            },
        },
        outDir: 'public',
        emptyOutDir: false,
    }
});