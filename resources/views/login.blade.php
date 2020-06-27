@extends('layout')

@section('content')

@isset($notice)
<div class="columns is-desktop">
    <div class="column is-half is-offset-one-quarter">
        <div class="notification is-danger">
            {{ $notice }}
        </div>
    </div>
</div>
@endisset

<div class="columns is-desktop">
    <div class="column is-half is-offset-one-quarter">
        <div class="notification is-warning">
            Your username/password to Stitcher is needed to verify
            you're a subscriber to Stitcher Premium. Your credentials
            are never stored on our system after your login, and the
            <a href="{{ env('SRC_URL') }}">
            source</a> to the service is available for those technically
            inclined.
        </div>
    </div>
</div>

<div class="columns">
<div class="column is-half is-offset-one-quarter">
    <form class="" method="post">
        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="email" name="email">
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" name="password">
            </div>
        </div>
        <input value="Login" type="submit" class="button is-primary ">
    </form>
</div>
</div>
@endsection
