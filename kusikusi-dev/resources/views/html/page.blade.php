<h1>Page: {{ $entity->contents['title'] ?? 'Page title' }}</h1>
{{ $entity->contents['body'] ?? '' }}
<form action="/form" method="post">
    <input name="name" />
    <input name="email" type="email" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="entity_id" value="{{ $entity->id }}" />
    <button type="submit">Enviar</button>
</form>
