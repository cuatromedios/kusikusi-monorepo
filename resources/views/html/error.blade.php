@extends('html_layout')
@section('main')
    <h1>Error {{ isset($status) ? $status : '' }}</h1>
    @if(isset($status))
        <p>
            @if($status === 404)
                Not found
            @elseif($status === 500)
                Internal server error
            @else
            @endif
        </p>
    @endif
    <p><a href="/">¿Regresar?</a></p>
    @if(env('APP_DEBUG'))
        <h2>{{ isset($message) ? $message : '' }}</h2>
        <p>{{ isset($file) ? $file : '' }}: {{ isset($line) ? $line : '' }}</p>
        @if(isset($trace))
            @foreach($trace as $t)
                <ol>
                    <li>
                        <strong>{{ $t['function'] ?? '' }} {{ $t['class'] ?? '' }}</strong>
                        {{ $t['file'] ?? '' }}:{{ $t['line'] ?? '' }}
                    </li>
                </ol>
            @endforeach
        @endif
    @endif
@endsection
