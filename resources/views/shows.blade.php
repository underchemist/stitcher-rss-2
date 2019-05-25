<?php declare(strict_types=1);

$user = app(\App\User::class);
$protocol = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
$domain = $_SERVER['HTTP_HOST'];
$base_uri = "://{$user->rss_user}:{$user->rss_password}@{$domain}";

?>
@extends('layout')
@section('content')

<div class="columns is-desktop">
    <div class="column is-half is-offset-one-quarter has-text-centered">
        <form method="GET">
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input class="input" name="term" type="text" placeholder="Find a show">
                </div>
                <div class="control">
                    <input type="submit" value="Search" class="button is-primary">
                </div>
            </div>
        </form>
    </div>
</div>

@if ($feeds && count($feeds))

<div class="columns is-desktop">
    <div class="column is-half is-offset-one-quarter">
    To add to your podcast client, copy the link, and paste into
        your podcast client without modification.
    </div>
</div>
<div class="columns is-desktop">
    <div class="column is-half is-offset-one-quarter">
        <div class="notification is-warning">
            Prompted for credentials? For your security, <strong>do not
                </strong> enter your Stitcher email/password into your
                podcast app. Use these generated numbers instead
                (created especially for you):
            <br>
            <br>
            <div style="font-family: monospace">
                Username: <strong>{{ $user->rss_user }}</strong><br>
                Password: <strong>{{ $user->rss_password }}</strong>
            </div>
        </div>
    </div>
</div>
<div class="columns is-desktop">
    <div class="column is-half is-offset-one-quarter has-text-centered">
        @foreach ($feeds as $feed)
        <?php $feed_url = $base_uri . "/shows/" . $feed->id . "/feed"; ?>
        <div class="box">
        <article class="media">
            <div class="media-left">
            <figure class="image is-82x82">
                <img src="{{
                    str_replace(
                        'http://cloudfront.assets.stitcher.com',
                        'https://s3.amazonaws.com/stitcher.assets',
                        $feed->image_url
                    )
                }}" alt="Image">
            </figure>
            </div>
            <div class="media-content">
            <div class="content">
                <p>
                <strong>{{ $feed->title }}</strong>
                <br>
                <input class='feed-url' value="{{ $protocol . $feed_url }}">
                </p>
            </div>
            <a class="button is-small is-primary is-outlined copy-feed">
                Copy Feed URL
            </a>
            <a class="button is-small is-primary is-outlined" href="{{ 'itpc' . $feed_url }}">
                Subscribe
            </a>
            </div>
        </article>
        </div>
        @endforeach
    </div>
</div>

<script>
    (function() {
        "use strict";

        var clip = function () {
            var el = this.parentElement.querySelector('.feed-url');
            el.select();

            try {
                var successful = document.execCommand('copy');
                el.blur();
                alert("Copied to clipboard.");
            } catch (err) {}

        };

        document.querySelectorAll('.copy-feed').forEach(function (item, idx) {
            item.addEventListener('click', clip);
        });
    })();
</script>

@endif

@endsection
