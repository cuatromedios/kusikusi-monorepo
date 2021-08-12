<x-entity>
  <h3>Update</h3>
  <form action="{{ route('entities.update', ['entity' => $entity->id]) }}" method="POST">
    @csrf
    <input name="_method" type="hidden" value="PUT">
    <label>Id
      <input type="text" name="id" value="{{$entity->id}}">
    </label>
    <label>Model
      <input type="text" name="model" value="{{$entity->model}}">
    </label>
    <label>View
      <input type="text" name="view" value="{{$entity->view}}">
    </label>
    <label>Parent
      <select name="parent_entity_id">
        <option value="">- None</option>
        @foreach ($entities as $item)
          <option value="{{ $item->id }}" @if ($item->id === $entity->parent_entity_id) selected @endif>{{ $item->id }} - {{ $item->model }}</option>
        @endforeach
      </select>
    </label>
    <label>Visibility
      <select name="visibility">
        <option value="public" @if ($entity->visibility === 'public') selected @endif>Public</option>
        <option value="draft" @if ($entity->visibility === 'draft') selected @endif>Draft</option>
        <option value="private" @if ($entity->visibility === 'private') selected @endif>Private</option>
      </select>
    </label>
    <label>Properties
      <textarea name="properties"></textarea>
    </label>
    <br>
    <button type="submit">Submit</button>
  </form>
</x-entity>
