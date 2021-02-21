<x-entity>
    <h3>Show</h3>

    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}

    <h4>Entity with contents</h4>
    {!! pretty_json($entityWithContents) !!}

    <h4>Entity with contents by field</h4>
    {!! pretty_json($entityWithContentsByField) !!}

    <h4>Entity with contents by language</h4>
    {!! pretty_json($entityWithContentsByLang) !!}

    <a href="{{ route('entities.create') }}">Create</a>
    
</x-entity>