let mix = require('laravel-mix')

mix.setPublicPath('dist')
    .js('resources/js/views.js', 'js')
    .sass('resources/sass/views.scss', 'css')
    .webpackConfig({
        resolve: {
            alias: {
                '@nova': path.resolve(__dirname, '../../../vendor/laravel/nova/resources/js/')
            }
        }
    })
