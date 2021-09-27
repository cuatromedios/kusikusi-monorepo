
    <h1>Page: {{ $entity->contents['title'] ?? 'Page title' }}</h1>
    {!! pretty_json($entity) !!}
