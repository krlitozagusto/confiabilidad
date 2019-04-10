<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" rel="stylesheet">
    <title>Confiabilidad</title>
</head>
<body class="pp header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
<div id="app">
    <router-view></router-view>
</div>
</body>
<script src="{{mix('js/app.js')}}"></script>
</html>
