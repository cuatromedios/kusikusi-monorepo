@extends('html_layout')
@section('main')
    <h1>{{ $entity->title }}</h1>
    <p>{{ $entity->welcome }}</p>
<div>
    <ul>
        @forelse ($children as $child)
           <li><a href="{{ $child->route }}">{{ $child->title }}</a></li>
        @empty
           <li>No children</li>
        @endforelse
    </ul>
</div>
@endsection
