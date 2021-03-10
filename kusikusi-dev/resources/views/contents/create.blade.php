<x-content>
    <h3>Create</h3>
    <form action="{{ route('contents.store') }}" method="POST">
        @csrf
        <label>Entity
            <input type="text" name="entity_id" value="{{ $entity_id }}" readonly>
        </label>
        <label>Lenguage
            <select name="lang">
                <option value="es">Spanish</option>
                <option value="en">English</option>
            </select>
        </label>
        <label>Field
            <input type="text" name="field">
        </label>
        <label>Text
            <textarea name="text"></textarea>
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
</x-content>
