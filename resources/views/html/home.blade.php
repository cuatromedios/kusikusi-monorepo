@extends('html_layout')
@section('title', $entity->title)
@section('description', $entity->description)
@section('main')
    <h2>{{ $entity->title }}</h2>

    @if($heros->isNotEmpty())
        <div class="hero-slider">
            @foreach($heros as $hero)
                <img src="{{ $hero->slide }}" alt="{{ $hero->title }}" />
            @endforeach
        </div>
    @endif

    <p>{{ $entity->welcome }}</p>
    <ul>
        @forelse ($children as $child)
           <li>
               <a href="{{ $child->route }}">
                   @if($child->medium)<img src="{{ $child->medium->icon }}" alt="{{ $child->medium->title }}">@endif
                   {{ $child->title }}
               </a>
           </li>
        @empty
           <li>No children</li>
        @endforelse
    </ul>
@endsection
