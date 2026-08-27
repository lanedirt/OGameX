import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs'
import { resolve } from 'node:path'
import { createHash } from 'node:crypto'

const ingameScripts = [
    'resources/js/ingame/jquery-1.12.4.min.js',
    'resources/js/ingame/vendor/jquery-ui-1.12.1.js',
    'resources/js/ingame/vendor/anythingslider-1.9.7.js',
    'resources/js/ingame/vendor/jquery.ba-bbq-1.4pre.js',
    'resources/js/ingame/vendor/jquery.colorpicker.js',
    'resources/js/ingame/jquery.js',
    'resources/js/ingame/inventory.js',
    'resources/js/ingame/jquery-spinners.js',
    'resources/js/ingame/messages.js',
    'resources/js/ingame/tooltips.js',
    'resources/js/ingame/trader.js',
    'resources/js/ingame/timerhandler.js',
    'resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js',
    'resources/js/ingame/messages-pagination.js',
    'node_modules/pusher-js/dist/web/pusher.min.js',
    'node_modules/laravel-echo/dist/echo.iife.js',
    'resources/js/ingame/echo.js',
    'resources/js/ingame/chat.js',
]

const outgameScripts = [
    'resources/js/outgame/6b1759b4d8ae0aeb3b4f566299ad46.js',
    'resources/js/outgame/22838c9f0f7e8e3535367164b832ce.js',
    'resources/js/outgame/22ef0d59ed3309209b51ac1d7d8674.js',
    'resources/js/outgame/f02d853270851b55790fb41a4113e9.js',
    'resources/js/outgame/799ec2f0eba935380926ea7756db23.js',
    'resources/js/outgame/d0437255213d95b42db39070285d8c.js',
    'resources/js/outgame/0b5c68ed173515e7cb0965c287aa0c.js',
    'resources/js/outgame/4c590fd581de4bc24b47347d879e94.js',
    'resources/js/outgame/6871e1cb7f618a30edcba23801e23c.js',
    'resources/js/outgame/0136dd84cb21c44f18865ec6f6b10a.js',
    'resources/js/outgame/60cd95d4ce5cb91a86861f433773d1.js',
    'resources/js/outgame/b55eb79922e157d28e811c7452ab10.js',
]

/**
 * Concatenates legacy (non-ESM) JS files and integrates them with the Vite manifest.
 *
 * These scripts use jQuery globals and cannot be loaded as ES modules, so they are
 * concatenated as plain text rather than bundled by Rollup.
 *
 * - Dev mode:  a middleware serves the concatenated content at the source path so
 *              @vite() resolves correctly against the dev server.
 * - Build mode: files are written to the output dir and entries are injected into
 *               manifest.json so @vite() resolves correctly from the manifest.
 */
function concatLegacyBundles(bundles, outDir = 'public/build') {
    let command

    return {
        name: 'concat-legacy-bundles',

        configResolved(config) {
            command = config.command
        },

        configureServer(server) {
            for (const { src, files } of bundles) {
                server.middlewares.use((req, res, next) => {
                    if ((req.url ?? '').split('?')[0] === `/${src}`) {
                        const content = files
                            .map(f => readFileSync(resolve(f), 'utf-8'))
                            .join(';\n')
                        res.setHeader('Content-Type', 'application/javascript')
                        res.end(content)
                    } else {
                        next()
                    }
                })
            }
        },

        closeBundle: {
            order: 'post',
            handler() {
                if (command !== 'build') return

                const manifestPath = resolve(outDir, 'manifest.json')
                let manifest = {}
                try {
                    manifest = JSON.parse(readFileSync(manifestPath, 'utf-8'))
                } catch {
                    // manifest may not exist on first run
                }

                mkdirSync(resolve(outDir, 'assets'), { recursive: true })

                for (const { src, files, name } of bundles) {
                    const content = files
                        .map(f => readFileSync(resolve(f), 'utf-8'))
                        .join(';\n')

                    const hash = createHash('sha256')
                        .update(content)
                        .digest('hex')
                        .slice(0, 8)

                    const fileName = `assets/${name}-${hash}.js`
                    writeFileSync(resolve(outDir, fileName), content)
                    manifest[src] = { file: fileName, src, isEntry: true }
                }

                writeFileSync(manifestPath, JSON.stringify(manifest, null, 2))
            },
        },
    }
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/ingame.css',
                'resources/css/outgame.css',
            ],
            refresh: [
                'resources/js/**',
                'resources/views/**',
            ],
        }),
        concatLegacyBundles([
            { src: 'resources/js/ingame.js', files: ingameScripts, name: 'ingame' },
            { src: 'resources/js/outgame.js', files: outgameScripts, name: 'outgame' },
        ]),
    ],
})
