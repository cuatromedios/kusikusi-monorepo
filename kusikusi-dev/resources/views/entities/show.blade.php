<x-entity>
    <h3>Show</h3>

    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}

    <h4>Entity with contents</h4>
    {!! pretty_json($entityWithContents) !!}

    <a href="{{ route('contents.create', ['idEntity' => $entity->id]) }}">Create content</a>

    <h4>Entity with contents by field</h4>
    {!! pretty_json($entityWithContent) !!}

    <h4>Entity with relations</h4>
    {!! pretty_json($entityWithRelations) !!}

    <a href="{{ route('entities.create') }}">Create</a>
    
</x-entity>