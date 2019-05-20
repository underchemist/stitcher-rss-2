@extends('layout')

@section('content')

@isset($notice)
    <h2>{{ $notice }}</h2>
@endisset

<form method="post">
    <input placeholder="email" name="email" type="email" />
    <input placeholder="password" name="password" type="password" />
    <button type="submit">Login</button>
</form>

@endsection
