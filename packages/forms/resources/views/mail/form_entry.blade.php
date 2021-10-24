<p style="font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: larger">{{ $entity->content['title']  }} ({{ $entry->id }}) </p>
<p style="font-family: Arial, Helvetica, sans-serif;">
    From: <strong>{{ $payload['name'] }}</strong><br>
    Email: <strong>{{ $payload['email'] }}</strong>
</p>

<ul style="font-family: Arial, Helvetica, sans-serif; font-size: larger; margin-top: 1rem">
@foreach($payload as $key => $value)
    <li>{{ __($key) }}: <strong>{{ $value  }}</strong></li>
@endforeach
</ul>
