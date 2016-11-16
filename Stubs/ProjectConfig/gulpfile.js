const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

var publicSass = [
	// use a wildcard or a file name, but not a directory
	'./resources/assets/sass/home.scss'
];

var dashboardJs = [
	'./resources/assets/components/jsonEditor/jsonListEditor.js',
];

var dashboardCss = [
	'./resources/assets/sass/dashboard/dashboard.scss'
];

var authCss = [
	'./resources/assets/sass/auth.scss'
];

var commonSassToWatch = [
    './resources/assets/scss/modules/*.scss',
    './resources/assets/scss/app/*.scss'
];

// Config paths
elixir.config.publicPath = 'public_html';
elixir.config.publicDir  = 'public_html';

// Run elixir
elixir(function (mix) {
	// sass
	mix.sass(publicSass, './public_html/css/dist/public.css');
	mix.sass(dashboardCss, './public_html/css/dist/dashboard.css');
	mix.sass(authCss, './public_html/css/dist/auth.css');

	Elixir.tasks.byName('sass').forEach(function (task) {
		task.watch(publicSass);
		task.watch(dashboardJs);
		task.watch(dashboardCss);
		task.watch(authCss);
		task.watch(commonSassToWatch);
	});

	// js
	mix.scripts(dashboardJs, './public_html/js/dist/dashboard.js');

	// user js
	// mix.scripts([
	//     './resources/assets/js/angular_app/user/**/*.js'
	// ], './public_html/js/angular_app/user/dist/app.js');

	// version
	mix.version([
		'css/dist/public.css',
		'css/dist/dashboard.css',
		'css/dist/auth.css',
		'js/dist/dashboard.js'
	]);

	mix.browserSync({proxy: 'localhost.devv'});

});