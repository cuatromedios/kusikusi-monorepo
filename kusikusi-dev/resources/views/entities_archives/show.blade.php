<x-entity_archive>
    <h3>Show</h3>

    <h4>Raw Entity</h4>
    {!! pretty_json($entity_archive) !!}
    
    <h4>Entity Archive with payload raw</h4>
        {!! pretty_json($archive_payload) !!}

    <a href="{{ route('entities_archives.create') }}">Create</a>
</x-entity_archive>