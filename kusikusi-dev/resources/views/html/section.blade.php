
    <h1>Section {{ $entity->content['title']  }} (6)</h1>
    <h3>This is entity home</h3>
    {!! pretty_json($entity) !!}
    @if ($children)
        <h3>These are children of entity</h3>
        @foreach ($children as $child)
        <p>
            {!! pretty_json($child) !!}
        </p>
        @endforeach
    @endif
