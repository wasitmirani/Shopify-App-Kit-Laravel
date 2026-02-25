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

mix.js('resources/js/storefront/iconito-front.jsx','js/')
    .react();

mix.copy('public/js/iconito-front.js', 'theme-app-extension/dev-extension/extensions/iconito-v2/assets/iconito-front.js');
mix.copy('public/js/iconito-front.js', 'theme-app-extension/staging-extension/extensions/iconito-v2/assets/iconito-front.js');
mix.copy('public/js/iconito-front.js', 'theme-app-extension/production-extension/extensions/iconito-v2/assets/iconito-front.js');
