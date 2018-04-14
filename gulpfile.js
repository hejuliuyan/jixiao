var elixir = require('laravel-elixir');
require('laravel-elixir-livereload');
require('laravel-elixir-compress');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
var production = elixir.config.production;
var basejs = [
    'resources/assets/js/vendor/jquery-1.10.2.min.js',
    'resources/assets/js/vendor/jquery.pjax.js',
    'resources/assets/js/vendor/jquery.form.js',
    'resources/assets/js/vendor/bootstrap.min.js',
    'resources/assets/js/vendor/bootstrap-notify.min.js',
    'resources/assets/js/vendor/es6-promise.auto.min.js',
    'resources/assets/js/vendor/sweetalert2.min.js',
    'resources/assets/js/vendor/moment.min.js',
    'resources/assets/js/vendor/math.js'
];

elixir(function(mix) {

    mix.copy([
        'node_modules/bootstrap-sass/assets/fonts/bootstrap'
    ], 'public/assets/fonts/bootstrap');

    mix.copy([
        'node_modules/font-awesome/fonts'
    ], 'public/assets/fonts/font-awesome');

    mix.sass([
        'base.scss',
        'main.scss'
    ], 'public/assets/css/styles.css');

    mix.scripts(basejs,
        'public/assets/js/scripts.js');

    mix.scripts([
        'base.js',
        'public.js'
    ], 'public/assets/js/main.js');

    mix.version([
        'assets/js/scripts.js',
        'assets/js/main.js',
        'assets/css/styles.css'
    ]);

    if (production) {
        mix.compress();
    }
});

