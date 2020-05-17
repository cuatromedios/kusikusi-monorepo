@extends('html_layout')
@section('title', $entity->title)
@section('description', $entity->description)
@section('main')
    <h2>{{ $entity->title }}</h2>
    <p>{{ $entity->welcome }}</p>
    <ul>
        @forelse ($children as $child)
           <li><a href="{{ $child->route }}">{{ $child->title }}</a></li>
        @empty
           <li>No children</li>
        @endforelse
    </ul>
@endsection
