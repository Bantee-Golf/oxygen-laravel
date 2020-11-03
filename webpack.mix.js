const mix = require('laravel-mix');

mix.setPublicPath('publish/public/');

mix.js('publish/resources/js/public.js', 'js/dist')
	.js('publish/resources/js/backend.js', 'js/dist')

	.sass('publish/resources/sass/oxygen/bootstrap.scss', 'css/dist/bootstrap.css')
	.sass('publish/resources/sass/public.scss', 'css/dist/public.css')
	.sass('publish/resources/sass/backend.scss', 'css/dist/backend.css')
	.sass('publish/resources/sass/auth.scss', 'css/dist/auth.css')
	.version()
	.sourceMaps()
;
