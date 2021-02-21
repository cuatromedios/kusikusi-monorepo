<x-entity>
    <h3>Show</h3>
    <h4>Raw Entity</h4>
    {!! pretty_json($entity) !!}
    <a href="{{ route('entities.create') }}">Create</a>
    
</x-entity>