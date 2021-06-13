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

mix.js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('autoprefixer'),
  ])
  // Recursos globales
  .css('resources/theme/plugins/fontawesome-free/css/all.css', 'public/css/fontawesome-free').sourceMaps()

  // Recursos para el panel de administración
  .js('resources/js/admin/main.js', 'public/js/admin')
  .css('resources/css/admin/main.css', 'public/css/admin').sourceMaps()
  .styles('resources/css/admin/jquery.nestable.css', 'public/css/admin/jquery.nestable.css').sourceMaps()
  .scripts([
    'resources/theme/plugins/jquery/jquery.js',
    'resources/theme/plugins/bootstrap/js/bootstrap.bundle.js',
    'resources/theme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.js',
    'resources/theme/dist/js/adminlte.js'
  ], 'public/js/admin/all.js').sourceMaps()
  .scripts('resources/js/admin/jquery.nestable.js', 'public/js/admin/jquery.nestable.js').sourceMaps()

  //Codigo para el componente del estanque
  .js('resources/js/admin/fishpond-component/app.js', 'public/js/admin/fishpond-component/app.js').sourceMaps();

if (mix.inProduction()) {
  mix.version();
}
