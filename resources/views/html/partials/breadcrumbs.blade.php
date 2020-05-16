<ol>
@foreach($ancestors as $ancestor)
        <li><a href="{{ $ancestor->route }}">{{ $ancestor->title }}</a></li>
@endforeach
</ol>
