import { defineConfig } from 'vite';
import path from 'path';
import fs from 'fs';

// We control the vite options rather than using the Laravel plugin, due to the fact we have a range of scripts
// within the blade files themselves, and using the Laravel plugin / directives mean the scripts become deferred
// when we need them to load instantly (so the other blade scripts etc, can access the bits they need)
// this prevents a re-write of everything. If you see a better way, feel free to PR!

const outputPaths = [
    'public/build/js/ingame.js',
    'public/build/js/outgame.js',
    'public/build/css/ingame.css',
    'public/build/css/outgame.css',
];

// Pre-delete target files manually before build
outputPaths.forEach(file => {
    const fullPath = path.resolve(__dirname, file);
    if (fs.existsSync(fullPath)) {
        fs.unlinkSync(fullPath); // delete existing file
    }
});

export default defineConfig({
    build: {
        minify: false,
        minifyIdentifiers: false,
        outDir: 'public',
        emptyOutDir: false, // we need to keep other files, but we manually delete just the needed ones above..
        rollupOptions: {
            treeshake: false,
            input: {
                ingame: path.resolve(__dirname, 'resources/js/ingame.js'),
                outgame: path.resolve(__dirname, 'resources/js/outgame.js'),
                ingameStyle: path.resolve(__dirname, 'resources/css/ingame.css'),
                outgameStyle: path.resolve(__dirname, 'resources/css/outgame.css'),
            },
            output: {
                format: 'commonjs',
                entryFileNames: 'build/js/[name].js',
                assetFileNames: assetInfo => {
                    if (assetInfo.name?.endsWith('.css')) {
                        return 'build/css/[name][extname]';
                    }
                    return 'build/assets/[name][extname]';
                },
            },
            
        },
    },
});
