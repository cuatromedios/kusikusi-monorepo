@if(isset($entity))
    <link rel="canonical" href="{{ config('cms.app_url', '') }}{{ $entity->route }}" />
    @foreach ($entity->routes as $route)
    <link rel="alternate" hreflang="{{ $route->lang }}" href="{{ config('cms.app_url', '') }}{{ $route->path }}" />
    @endforeach
@endif
