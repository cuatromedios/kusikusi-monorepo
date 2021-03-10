<x-entity_archive>
    <h3>Index</h3>
    <table>
        <thead>
            <tr>
                <th>entity_id</th>
                <th>archive_id</th>
                <th>kind</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entities_archives as $archive)
            <tr>
                <td>
                    <a href="{{ route('entities_archives.show', $archive->entity_id) }}">{{ $archive->entity_id }}</a>
                </td>
                <td>{{ $archive->archive_id }}</td>
                <td>{{ $archive->kind }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="{{ route('entities_archives.create') }}">Create</a>
    <a href="{{ route('entities_archives.restore') }}">Restore Entity</a>
    {!! pretty_json($entities_archives) !!}

</x-entity_archive>