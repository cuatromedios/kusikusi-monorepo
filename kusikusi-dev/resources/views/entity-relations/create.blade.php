<x-entity-relation>
    <h3>Create relationship between models</h3>
    <form action="{{ route('entities-relations.store') }}" method="POST">
        @csrf
        <label>Caller Entity
            <input type="text" name="caller_entity_id" value="{{ $entity_id }}" readonly>
        </label>
        <label>Called Entity
            <select name="called_entity_id">
                <option value="">- None</option>
                @foreach ($entities as $entity)
                <option value="{{ $entity->id }}">{{ $entity->id }} - {{ $entity->model }}</option>
                @endforeach
            </select>
        </label>
        <label>Kind
            <input type="text" name="kind">
        </label>
        <label>Position
            <input type="text" name="position">
        </label>
        <label>Depth
            <input type="text" name="depth">
        </label>
        <label>Tags
            <textarea name="tags"></textarea>
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
</x-entity-relation>