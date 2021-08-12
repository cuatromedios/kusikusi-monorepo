<x-entity>
    <h3>Index</h3>
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>model</th>
                <th>parent</th>
                <th>title</th>
                <th>actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entities as $entity)
            <tr>
                <td>
                    <a href="{{ route('entities.show', $entity->id) }}">{{ $entity->id }}</a>
                </td>
                <td>{{ $entity->model }}</td>
                <td>{{ $entity->parent_entity_id }}</td>
                <td>{{ $entity->content['title'] ?? ''}}</td>
                <td><a href="{{ route('entities.edit', ['entity' => $entity->id]) }}">Edit</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('entities.create') }}">Create</a>
    {!! pretty_json($entities) !!}
</x-entity>
