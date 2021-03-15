<x-medium>
    <h3>Index</h3>
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>model</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entities as $entity)
            <tr>
                <td>
                    <a href="{{ route('media-entities.show', $entity->id) }}">{{ $entity->id }}</a>
                </td>
                <td>{{ $entity->model }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {!! pretty_json($entities) !!}
</x-medium>