let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.setPublicPath('public_html/');

mix.js('resources/assets/js/dashboard.js', 'public_html/js/dist')
	.sass('resources/assets/sass/home.scss', 'public_html/css/dist/public.css')
	.sass('resources/assets/sass/dashboard/dashboard.scss', 'public_html/css/dist/dashboard.css')
	.sass('resources/assets/sass/auth.scss', 'public_html/css/dist/auth.css')
	.version()
	.sourceMaps();

mix.browserSync({proxy: 'localhost.dev'});