@extends('html_layout')
@section('title', $entity->title)
@section('description', $entity->description)
@section('main')
    <h2>{{ $entity->title }}</h2>

    @if($heros->isNotEmpty())
        <div class="hero-slider">
            @foreach($heros as $hero)
                <img src="{{ $hero->thumb }}" alt="{{ $hero->title }}" />
            @endforeach
        </div>
    @endif

    <p>{{ $entity->welcome }}</p>
    <ul>
        @forelse ($children as $child)
           <li><a href="{{ $child->route }}">{{ $child->title }}</a></li>
        @empty
           <li>No children</li>
        @endforelse
    </ul>
@endsection
