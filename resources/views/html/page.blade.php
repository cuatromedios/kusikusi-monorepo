@extends('html_layout')
@section('title', $entity->title)
@section('description', $entity->description)
@section('main')
    @include('html.partials.breadcrumbs')
    <h1>{{ $entity->title }}</h1>
    <em>{{ $entity->description }}</em>
    {{ $entity->body }}
<div>
    @forelse ($media as $medium)
        <img src="{{ $medium->thumb }}" alt="" />
    @empty
        <em>No children</em>
    @endforelse
</div>
@endsection
