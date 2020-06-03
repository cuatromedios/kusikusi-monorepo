@extends('html_layout')
@section('title', $entity->title)
@section('description', $entity->description)
@section('main')
    @include('html.partials.breadcrumbs')
    <h1>{{ $entity->title }}</h1>
    <em>{{ $entity->description }}</em>

    @if($images->isNotEmpty())
        <div class="slider">
            @foreach($images as $image)
                <img src="{{ $image->thumb }}" alt="{{ $image->title }}" />
            @endforeach
        </div>
    @endif

    {{ $entity->body }}

    <h2>Documents</h2>
    <ul class="documents">
        @forelse($docs as $doc)
            <li><a href="{{ $doc->original }}" download>{{ $doc->title }}</a></li>
        @empty
            <li>No documents for download</li>
        @endforelse
    </ul>
@endsection
