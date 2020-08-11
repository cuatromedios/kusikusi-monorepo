@if(isset($entity))
    <p>
    @lang('texts.lang'):
    @foreach ($entity->routes as $route)
        @if(in_array($route->lang, config('cms.langs')))
            <a href="{{ $route->path }}">@lang("texts.langs.$route->lang")</a>
        @endif
    @endforeach
    </p>
@endif
