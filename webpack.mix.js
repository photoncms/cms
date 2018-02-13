let mix = require('laravel-mix');

var path = require('path');

mix.setPublicPath('public');

mix.options({ processCssUrls: false });

mix.disableSuccessNotifications()

    .less('resources/assets/photonCms/core/less/proton.less', 'public/css')

    .less('resources/assets/photonCms/core/less/bootstrap.less', 'public/css')

    .less('resources/assets/photonCms/dependencies/less/custom.less', 'public/css')

    .styles([
        'resources/assets/photonCms/core/css/vendor/bootstrap-switch.css',
        'resources/assets/photonCms/core/css/vendor/cropper/cropper.css',
        'resources/assets/photonCms/core/css/vendor/select2/select2.css',
        'resources/assets/photonCms/core/css/vendor/select2/select2-bootstrap.css',
        'resources/assets/photonCms/core/css/vendor/jstree-theme/proton/style.css',
        'resources/assets/photonCms/core/css/vendor/uniformjs/css/uniform.default.css',
        'resources/assets/photonCms/core/css/vendor/nprogress.css',
        'resources/assets/photonCms/core/css/vendor/redactor.css',
        'resources/assets/photonCms/core/css/vendor/bootstrap-iconpicker.css',
        'resources/assets/photonCms/core/css/vendor/animate.css',
        'resources/assets/photonCms/core/css/font-awesome.css',
        'resources/assets/photonCms/core/css/font-titillium.css',
        'resources/assets/photonCms/core/css/vendor/jquery.pnotify.default.css'

    ], 'public/css/vendor.css')

        .copyDirectory('resources/assets/photonCms/core/images', 'public/images')

        .copyDirectory('resources/assets/photonCms/dependencies/images', 'public/images')

        .copyDirectory('resources/assets/photonCms/core/css/fonts', 'public/css/fonts')

        .copyDirectory('resources/assets/photonCms/core/css/vendor', 'public/css/vendor')

    .js('resources/assets/photonCms/core/js/main.js', 'public/js/app.js')

    .version()

    .sourceMaps()

        .scripts([
            'resources/assets/photonCms/core/js/vendor/jquery-3.2.1.min.js',
            'resources/assets/photonCms/core/js/vendor/jquery.cookie.js',
            'resources/assets/photonCms/core/js/vendor/nprogress.js',
            'resources/assets/photonCms/core/js/vendor/bootstrap-datetimepicker.js',
            'resources/assets/photonCms/core/js/vendor/bootstrap-switch.js',
            'resources/assets/photonCms/core/js/vendor/cropper.js',
            'resources/assets/photonCms/core/js/vendor/Chart.min.js',
            'resources/assets/photonCms/core/js/vendor/fileinput.js',
            'resources/assets/photonCms/core/js/vendor/jquery-ui.min.js',
            'resources/assets/photonCms/core/js/vendor/jquery.imgareaselect.js',
            'resources/assets/photonCms/core/js/vendor/jquery.pnotify.min.js',
            'resources/assets/photonCms/core/js/vendor/jquery.uniform.min.js',
            'resources/assets/photonCms/core/js/vendor/jquery.ui.touch.js',
            'resources/assets/photonCms/core/js/vendor/jstree.min.js',
            'resources/assets/photonCms/core/js/vendor/modernizr.js',
            'resources/assets/photonCms/core/js/vendor/moment-with-locales.js',
            'resources/assets/photonCms/core/js/vendor/parsley.min.js',
            'resources/assets/photonCms/core/js/vendor/redactor.js',
            'resources/assets/photonCms/core/js/vendor/select2.full.min.js',
            'resources/assets/photonCms/core/js/vendor/bootstrap.min.js',
            'resources/assets/photonCms/core/js/vendor/favico.js',
            'resources/assets/photonCms/core/js/vendor/iconset/iconset-fontawesome-4.7.0.js',
            'resources/assets/photonCms/core/js/vendor/bootstrap-iconpicker.js',
        ], 'public/js/vendor.js')

        .version()

    .browserSync({
        proxy: 'photoncms.test'
    });

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/assets/photonCms/core/js/config'),
            '_': path.resolve(__dirname, 'resources/assets/photonCms/core/js'),
            '~': path.resolve(__dirname, 'resources/assets/photonCms/dependencies/js')
        }
    },
    module: {
        loaders: [
            {
                test: /\.json$/,
                exclude: /node_modules/,
                loader: 'json-loader'
            }
        ],
        rules: [
            {
                test: /\.md$/,
                use: 'raw-loader',
            }
        ]
    },
});
