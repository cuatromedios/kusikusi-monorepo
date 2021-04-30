<div>
    <figure>
        <picture>
            <img src="{{$entity->icon}}" alt="{{ $entity->content['title'] ?? '' }}">
        </picture>
        @if ($entity->content['summary'] ?? '')   
            <figcaption>{{ $entity->content['summary'] }}</figcaption>
        @endif
    </figure>
</div>