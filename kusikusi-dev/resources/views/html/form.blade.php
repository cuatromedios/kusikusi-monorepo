
<h1>Form</h1>
<h3>This is entity form</h3>
<form method="POST">
@foreach ($entity->properties['formfields'] as $item)
    <h4>Form field: {{ $item['type'] }}</h4>
    @switch($item['type'])
        @case('text')
            <label>
                {{$item['label'] ?? ''}}
                <input  type="text" id="{{$item['name'] ??''}}" name="{{$item['label'] ?? ''}}" placeholder="{{$item['placeholder'] ?? ''}}" @if ($item['required'] == true) required @endif>
            </label>
        @break
        @case('email')
            <label>
                {{$item['label'] ?? ''}}
                <input type="email" id="{{$item['name'] ??''}}" name="{{$item['label'] ?? ''}}" placeholder="{{$item['placeholder'] ?? ''}}" @if ($item['required'] == true) required @endif>
            </label>
        @break
        @case('textarea')
            <label>
                {{$item['label'] ?? ''}}
                <textarea id="{{$item['name'] ??''}}" name="{{$item['label'] ?? ''}}"  placeholder="{{$item['placeholder'] ?? ''}}" @if ($item['required'] == true) required @endif></textarea>
            </label>
        @break
        @case('checkbox')
            <label> 
                <input type="checkbox" id="{{$item['name'] ?? ''}}" name="{{$item['label'] ?? ''}}" value="{{$item['value'] ?? ''}}" @if ($item['required'] == true) required @endif>
                {{$item['label'] ?? ''}}
            </label>
        @break
        @case('radio')
            @foreach ($item['options'] as $option)
                <label> 
                    <input name="{{$item['name']}}" type="radio" value="{{$option['value']}}" @if ($item['required'] == true) required @endif>
                    {{$option['label']}}
                </label>
            @endforeach
        @break
        @case('select')
            <label> 
                {{$item['label'] ?? ''}}
                <select name="{{$item['label'] ?? ''}}" id="{{$item['name'] ?? ''}}" @if ($item['required'] == true) required @endif>
                    @foreach ($item['options'] as $option)
                        <option value="{{$option['value']}}">{{$option['name']}}</option>
                    @endforeach
                </select>
            </label>
        @break
        @default
    @endswitch
@endforeach
<input type="hidden" name="_token" value="{{ csrf_token() }}" /><br>
<button type="submit">Enviar</button>
</form>
{!! pretty_json($entity) !!}