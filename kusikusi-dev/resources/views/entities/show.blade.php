<x-entity>
    <h3>Show Entity {{ $entity->id }}</h3>

    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}

    <h4>Entity with contents</h4>
    {!! pretty_json($entityWithContents) !!}

    <h4>Entity with contents by field</h4>
    {!! pretty_json($entityWithContent) !!}

    <h4>Entity with relations</h4>
    {!! pretty_json($entityWithRelations) !!}

    @if (isset($childrenOfEntity) && count($childrenOfEntity) > 0)
        <h4>Children of Entity</h4>
        @foreach ($childrenOfEntity as $child)    
            {!! pretty_json($child) !!}
        @endforeach
    @endif

    @if (isset($parentOfEntity) && count($parentOfEntity) > 0)
        <h4>Parent of Entity</h4>
        @foreach ($parentOfEntity as $parent)    
            {!! pretty_json($parent) !!}
        @endforeach
    @endif

    @if (isset($ancestorsOfEntity) && count($ancestorsOfEntity) > 0)
        <h4>Ancestors of Entity</h4>
        @foreach ($ancestorsOfEntity as $ancestor)    
            {!! pretty_json($ancestor) !!}
        @endforeach
    @endif

    @if (isset($descendantsOfEntity) && count($descendantsOfEntity) > 0)
        <h4>Descendants of Entity</h4>
        @foreach ($descendantsOfEntity as $descendant)    
            {!! pretty_json($descendant) !!}
        @endforeach
    @endif
    
    @if (isset($siblingsOfEntity) && count($siblingsOfEntity) > 0)
        <h4>Siblings of Entity</h4>
        @foreach ($siblingsOfEntity as $sibling)    
            {!! pretty_json($sibling) !!}
        @endforeach
    @endif
    
    @if (isset($relatedByEntity) && count($relatedByEntity) > 0)
        <h4>Related by Entity</h4>
        @foreach ($relatedByEntity as $related)    
            {!! pretty_json($related) !!}
        @endforeach
    @endif
    
    @if (isset($relatingEntity) && count($relatingEntity) > 0)
        <h4>Relating Entity</h4>
        @foreach ($relatingEntity as $relating)    
            {!! pretty_json($relating) !!}
        @endforeach
    @endif

    <a href="{{ route('entities.create') }}">Create</a>
    
</x-entity>