<!doctype html>
<!--[if IE 9]><html class="no-js lt-ie10"><![endif]-->
<!--[if gt IE 9]><!-->
<html lang="{{ app()->getLocale() }}" class="no-js">
<!--<![endif]-->

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>Photon CMS</title>
        <link rel='shortcut icon' type='image/x-icon' href='/favicon.ico' />
        <link rel="stylesheet" href="/css/vendor.css">
        <link rel="stylesheet" href="/css/bootstrap.css">
        <link rel="stylesheet" href="/css/proton.css">
        <link rel="stylesheet" href="/css/custom.css">
    </head>

    <body class="login-page">
        <app id="app" class="vueAppContainer"></app>
        <script src="{{ mix('/js/vendor.js') }}"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
        <script>NProgress.configure({ minimum : 0.4 })</script>
    </body>
</html>
