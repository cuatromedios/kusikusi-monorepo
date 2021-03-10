<x-entity>
    <h3>Show Entity {{ $entity->id }}</h3>

    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}

    <h4>Entity with contents</h4>
    {!! pretty_json($entityWithContents) !!}

    <a href="{{ route('contents.create', ['entity_id' => $entity->id]) }}">Create content in the model</a>

    <h4>Entity with contents by field</h4>
    {!! pretty_json($entityWithContent) !!}

    <h4>Entity with relations</h4>
    {!! pretty_json($entityWithRelations) !!}
    <h4><a href="{{ route('entities-relations.create', ['entity_id' => $entity->id]) }}">Create relation in the model</a></h4>

    @if (isset($childrenOfEntity) && count($childrenOfEntity) > 0)
        <h4>Children of Entity</h4>
        {!! pretty_json($childrenOfEntity) !!}
    @endif

    @if (isset($parentOfEntity) && count($parentOfEntity) > 0)
        <h4>Parent of Entity</h4>    
        {!! pretty_json($parentOfEntity) !!}
    @endif

    @if (isset($ancestorsOfEntity) && count($ancestorsOfEntity) > 0)
        <h4>Ancestors of Entity</h4>)    
        {!! pretty_json($ancestorsOfEntity) !!}
    @endif

    @if (isset($descendantsOfEntity) && count($descendantsOfEntity) > 0)
        <h4>Descendants of Entity</h4>    
        {!! pretty_json($descendantsOfEntity) !!}
    @endif
    
    @if (isset($siblingsOfEntity) && count($siblingsOfEntity) > 0)
        <h4>Siblings of Entity</h4>
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

    <a href="{{ route('entities.create') }}">Create</a>
    
</x-entity>