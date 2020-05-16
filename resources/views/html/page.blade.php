@extends('html_layout')
@section('main')
    @include('html.partials.breadcrumbs')
    <h1>{{ $entity->model }}: {{ $entity->title }}</h1>
    <em>{{ $entity->summary }}</em>
    {{ $entity->body }}
<div>
    @forelse ($media as $mediumEntity)
        <img src="{{ $mediumEntity }}" alt="" />
    @empty
        <em>No children</em>
    @endforelse
</div>
@endsection
