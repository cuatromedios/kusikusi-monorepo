<x-entity>
    <h3>Show</h3>

    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}

    <h4>Entity with contents</h4>
    {!! pretty_json($entityWithContents) !!}

    <a href="{{ route('contents.create', ['entity_id' => $entity->id]) }}">Create content in the model</a>

    <h4>Entity with contents by field</h4>
    {!! pretty_json($entityWithContent) !!}

    <h4>Entity with relations</h4>
    {!! pretty_json($entityWithRelations) !!}
    <h4><a href="{{ route('contents.create', ['entity_id' => $entity->id]) }}">Create relation in the model</a></h4>


    <a href="{{ route('entities.create') }}">Create Model</a>
    
</x-entity>