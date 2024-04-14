const mix = require('laravel-mix');

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

mix.postCss('resources/css/ingame.css', 'public/css', [
        //
    ]).version();
mix.postCss('resources/css/outgame.css', 'public/css', [
    //
]).version();

mix.options({
    terser: {
        terserOptions: {
            keep_fnames: true
        }
    }
})