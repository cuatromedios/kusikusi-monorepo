<!DOCTYPE html>
<html lang="{{ isset($lang) ? $lang : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>@yield(('title'))</title>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body class="{{isset($entity) ? 'model-'.$entity->model.' view-'.$entity->view.' id-'.$entity->id : ''}}">
<header>

</header>
<main>
    @yield('main')
</main>
<footer>
A kusikusi website
</footer>
@include('html.partials.debug')
</body>
</html>
