<x-medium>
    <h3>Show Medium Entity {{ $entity->id }}</h3>
    {!! pretty_json($entity) !!}
    <form action="/media-entities/{{$entity->id}}/upload" method="POST"  enctype="multipart/form-data">
        @csrf
        <fieldset>
            <label for="file">Upload new file to this entity</label>
            <input type="file" name="file" id="file">
            <button type="submit">Upload</button>
        </fieldset>
    </form>
    <img src="{{$entity->icon}}" alt="{{$entity->content['title'] ?? ''}}">
</x-medium>