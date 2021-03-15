<x-medium>
    <h3>Show Medium Entity {{ $entity->id }}</h3>
    {!! pretty_json($entity) !!}
    <img src="{{$entity->icon}}" alt="{{$entity->content['title']}}">
</x-medium>