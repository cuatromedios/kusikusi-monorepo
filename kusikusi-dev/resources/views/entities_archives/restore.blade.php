<x-entity_archive>
    <h3>Restore Archive of Entity</h3>
    <form action="{{ route('entities_archives.restore_store') }}" method="POST">       
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <label>Entity Id
            <select name="entity_id">
                @foreach ($entities as $entity)
                        <option value="{{ $entity->id }}">{{ $entity->id }}</option>
                @endforeach
            </select>
        </label>
        <br>
        <label>Archive Id
            <select name="archive_id">
                @foreach ($entities_archives as $archive)
                    @if ($archive)
                        <option value="{{ $archive->archive_id }}">{{ $archive->archive_id }}</option>
                    @endif
                @endforeach
            </select>
        </label>
        <br>
        <button type="submit">Submit</button>
    </form>
</x-entity_archive>