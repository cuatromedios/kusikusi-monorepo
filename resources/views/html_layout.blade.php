<!DOCTYPE html>
<html lang="{{ isset($lang) ? $lang : 'en' }}">
<head>
    <meta charset="UTF-8">
    <title>@yield(('title'))</title>
    <meta name="description" content="@yield(('description'))">
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name="generator" content="Kusikusi CMS" />
    @include('html.partials.headlinks')
    @include('html.partials.favicons')
    @include('html.partials.socialshare')
</head>
<body class="{{isset($entity) ? 'model-'.$entity->model.' view-'.$entity->view : '' }}">
<header>
    <div class="container">
        @if(isset($website))
            {!! $entity->model === 'home' ? '<h1>' : '<p>' !!}
            @if(isset($logo))
                <img src="{{ $logo->logo }}" alt="{{ $website->title }}" />
            @endif
            {!! $entity->model === 'home' ? '</h1>' : '</p>' !!}
        @endif
        <nav>
            <ul>
            @foreach($mainMenu as $item)
                <li><a href="{{ $item->route }}">{{ $item->title }}</a></li>
            @endforeach
            </ul>
        </nav>
    </div>
</header>
<main class="container">
    @yield('main')
</main>
<footer class="container">
    <p>A kusikusi website.</p>
    @include('html.partials.langlist')
</footer>
@include('html.partials.debug')
</body>
</html>
