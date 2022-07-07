let mix = require('laravel-mix')

mix.setPublicPath('dist');
mix.js('resources/js/nova-custom-views.js', 'js')
    .sass('resources/sass/nova-custom-views.scss', 'css')
    .webpackConfig({
        resolve: {
            alias: {
                '@nova': path.resolve(__dirname, '../../laravel/nova/resources/js/'),
                '@': path.resolve(__dirname, '../../laravel/nova/resources/js/')
            }
        }
    })


