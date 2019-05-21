@extends('layout')
<?php
$user = app(\App\User::class);
$protocol = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$base_uri = "{$protocol}://{$user->rss_user}:{$user->rss_password}@{$domain}";
?>
@section('content')

<form method="get" action="/shows">
    <input name="q" type="text" placeholder="Search" value="{{ $term }}" />
    <button type="submit">Search</button>
</form>

<ul>
    @foreach($feeds as $feed)
        <li><a href="{{ $base_uri }}/shows/{{ $feed->id }}/feed">{{ $feed->title }}</a></li>
    @endforeach
</ul>

@endsection
