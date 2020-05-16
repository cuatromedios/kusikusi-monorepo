@extends('html_layout')
@section('title', $entity->title)
@section('description', $entity->description)
@section('main')
    @include('html.partials.breadcrumbs')
    <h1>{{ $entity->model }}: {{ $entity->title }}</h1>
    <em>{{ $entity->description }}</em>
<div>
    <ul>
        @forelse ($children as $child)
           <li>
               <a href="{{ $child->route }}.html">
                   @if($child->medium)
                   <img src="{{ $child->medium->thumb }}" alt="{{ $child->medium->title }}">
                   @endif
               {{ $child->title }}
               </a></li>
        @empty
           <li>No children</li>
        @endforelse
    </ul>
</div>
@endsection
