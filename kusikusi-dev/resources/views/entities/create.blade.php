<x-entity>
    <h3>Create</h3>
    <form action="{{ route('entities.store') }}" method="POST">
        @csrf
        <label>Id
            <input type="text" name="id">
        </label>
        <label>Model
            <input type="text" name="model">
        </label>
        <label>Parent
            <select name="parent_entity_id">
                <option value="">- None</option>
                @foreach ($entities as $entity)
                <option value="{{ $entity->id }}">{{ $entity->id }} - {{ $entity->model }}</option>
                @endforeach
            </select>
        </label>
        <label>Visibility
            <select name="visibility">
                <option value="public">Public</option>
                <option value="draft">Draft</option>
                <option value="private">Private</option>
            </select>
        </label>
        <label>Properties
            <textarea name="properties"></textarea>
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
</x-entity>