@extends('layout')

@section('content')

<form method="get" action="/shows">
    <input name="q" type="text" placeholder="Search" value="{{ $term }}" />
    <button type="submit">Search</button>
</form>

<ul>
    @foreach($feeds as $feed)
        <li><a href="/shows/{{ $feed->id }}/feed">{{ $feed->title }}</a></li>

@endforeach

@endsection
