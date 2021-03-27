
    <h1>@lang("website.error") {{ isset($status) ? $status : '' }}</h1>
    @if(isset($status))
        <p>@lang("website.errors.$status")</p>
    @endif
    <p><a href="/">@lang("website.back")</a></p>
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
