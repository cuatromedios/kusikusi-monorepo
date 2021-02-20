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
        <br>
        <button type="submit">Submit</button>
    </form>
</x-entity>