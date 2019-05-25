@extends('layout')

@section('content')

    <div class="columns is-hidden-desktop">
        <div class="column is-half is-offset-one-quarter content has-text-centered">
            <a class="button is-primary" href="/login">
                Login to Stitcher
            </a>
        </div>
    </div>

    <div class="columns is-desktop">
        <div class="column is-half is-offset-one-quarter content">
            <h2 class="title is-3">Frequently Asked Questions</h2>
            <strong>What is this?</strong>
            <p>
                This is a service that provides RSS feeds for premium
                content available to Stitcher subscribers. These RSS
                feeds can be added to most podcast clients (Pocket Casts,
                Podcast Addict, iTunes, etc.) to play Stitcher content
                like any other podcast.
            </p>
            <strong>Why do I have to give you my username/password?</strong>
            <p>
                Your username/password to Stitcher is needed to verify
                you're a subscriber to Stitcher Premium. Your credentials
                are never stored on our system after your login, and
                the <a href="https://gitlab.com/adduc-projects/stitcher-rss">
                source</a> to the service is available for those
                technically inclined.
            </p>
        </div>
    </div>
@endsection
