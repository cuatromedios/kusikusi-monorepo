<x-content>
    <h3>Create</h3>
    <form action="{{ route('contents.store') }}" method="POST">
        @csrf
        <label>Id
            <input type="text" name="id">
        </label>
        <label>Entity
            <input type="text" name="entity_id" value="{{ $id }}" readonly>
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
        <br>
        <button type="submit">Submit</button>
    </form>
</x-content>
