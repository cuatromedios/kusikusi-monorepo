@if(isset($entity))
    <link rel="canonical" href="{{ config('cms.app_url', '') }}{{ $entity->route }}" />
    @foreach ($entity->routes as $route)
        @if(in_array($route->lang, config('cms.langs')))
            <link rel="alternate" hreflang="{{ $route->lang }}" href="{{ config('cms.app_url', '') }}{{ $route->path }}" />
        @endif
    @endforeach
@endif
