@foreach($feeds as $feed)

<a href="/shows/{{ $feed->id }}/feed">{{ $feed->title }}

@endforeach
