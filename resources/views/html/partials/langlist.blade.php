@if(isset($entity))
    <p>
    @lang('texts.lang'):
    @foreach ($entity->routes as $route)
        <a href="{{ $route->path }}">@lang("texts.langs.$route->lang")</a>
    @endforeach
    </p>
@endif
