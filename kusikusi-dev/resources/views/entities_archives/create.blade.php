<x-entity_archive>
    <h3>Create Archive of Entity</h3>
    <form action="{{ route('entities_archives.store') }}" method="POST">
        @csrf
        <label>Entity id
            <select name="entity_id">
                @foreach ($entities as $entity)
                <option value="{{ $entity->id }}">{{ $entity->id }}</option>
                @endforeach
            </select>
        </label>
        <br>
        <label>Kind
            <select name="kind">
                <option value="version">Version</option>
                <option value="draft">Draft</option>
            </select>
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
</x-entity_archive>