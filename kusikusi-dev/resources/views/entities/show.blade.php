<x-entity>
    <p>Showing Entity: {{ $entity->id }}</p>

    <a href="{{ route('entities.edit', ['entity' => $entity->id]) }}">Edit</a>
    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}

    <h3>Contents</h3>
    <a href="{{ route('contents.create', ['entity_id' => $entity->id]) }}">Create content in the model</a>
    <h4>Entity with contents</h4>
    {!! pretty_json($entityWithContents) !!}

    <h4>Entity with contents by field</h4>
    {!! pretty_json($entityWithContent) !!}

    <h3>Relations</h3>
    <h4><a href="{{ route('entities-relations.create', ['entity_id' => $entity->id]) }}">Create relation in the model</a></h4>
    <h4>Entity with relations</h4>

    {!! pretty_json($entityWithRelations) !!}

    @if (isset($childrenOfEntity) && count($childrenOfEntity) > 0)
        <h4>Children</h4>
        {!! pretty_json($childrenOfEntity) !!}
    @endif

    @if (isset($parentOfEntity))
        <h4>Parent</h4>
        {!! pretty_json($parentOfEntity) !!}
    @endif

    @if (isset($ancestorsOfEntity) && count($ancestorsOfEntity) > 0)
        <h4>Ancestors</h4>
        {!! pretty_json($ancestorsOfEntity) !!}
    @endif

    @if (isset($descendantsOfEntity) && count($descendantsOfEntity) > 0)
        <h4>Descendants</h4>
        {!! pretty_json($descendantsOfEntity) !!}
    @endif

    @if (isset($siblingsOfEntity) && count($siblingsOfEntity) > 0)
        <h4>Siblings</h4>
        {!! pretty_json($siblingsOfEntity) !!}
    @endif

    @if (isset($relatedByEntity) && count($relatedByEntity) > 0)
        <h4>Related by Entity</h4>
        {!! pretty_json($relatedByEntity) !!}
    @endif

    @if (isset($relatingEntity) && count($relatingEntity) > 0)
        <h4>Relating Entity</h4>
        {!! pretty_json($relatingEntity) !!}
    @endif

    @if (isset($entityWithRoutes))
        <h3>Routes</h3>
        {!! pretty_json($entityWithRoutes) !!}
    @endif

</x-entity>
