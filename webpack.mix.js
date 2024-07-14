const mix = require('laravel-mix');
const webpack = require('webpack');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// Combine all JavaScript files into a single file. We don't use webpack for this because
// it conflicts with jQuery and global variables.

// ---
// Ingame
// ---
mix.scripts([
    'resources/js/ingame/jquery-1.12.4.min.js',
    'resources/js/ingame/jquery.js',
    'resources/js/ingame/chat.js',
    'resources/js/ingame/inventory.js',
    'resources/js/ingame/jquery-spinners.js',
    'resources/js/ingame/messages.js',
    'resources/js/ingame/tooltips.js',
    'resources/js/ingame/trader.js',
    //'resources/js/ingame/percentagebar.js',
    'resources/js/ingame/timerhandler.js',
    'resources/js/ingame/e7c74974620fa35b197315ebdbb8c2.js',
], 'public/js/ingame.js').minify('public/js/ingame.js').version();

// ---
// Outgame
// ---
mix.scripts([
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
], 'public/js/outgame.js').minify('public/js/outgame.js').version();

mix.postCss('resources/css/ingame.css', 'public/css', [
        //
    ]).version();
mix.postCss('resources/css/outgame.css', 'public/css', [
    //
]).version();