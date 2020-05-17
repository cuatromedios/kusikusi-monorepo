@if(isset($entity))
@php
// Get the social image from the meiudm tagged appended to the entity, if none, then the medium appended
// to the website entity, if not, a default image
$social_image = config('cms.app_url', '') . Illuminate\Support\Arr::get($entity, 'medium.preview', Illuminate\Support\Arr::get($website, 'medium.preview', '/favicons/social.png'));
@endphp
<meta itemprop="name" content="@yield('title')">
<meta itemprop="description" content="@yield('description')">
<meta itemprop="image" content="{{ $social_image }}">
<meta property="og:title" content="@yield('title')">
<meta property="og:image" content="{{ $social_image }}">
<meta property="og:description" content="@yield('description')">
<meta property="og:url" content="{{ config('cms.app_url', '') }}">
<meta property="og:site_name" content="{{ isset($website) ? $website->title : '' }}">
<meta property="og:locale" content="{{ $lang ?? 'en' }}">
<meta property="og:type" content="website">
@endif
