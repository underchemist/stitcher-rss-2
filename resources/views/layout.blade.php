@inject('user', 'App\User')

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="bundle.js" type="application/javascript"></script>
    <title>Unofficial RSS Feeds for Stitcher Premium</title>
    @if (env('GA_TRACKING_ID'))
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', '{{env('GA_TRACKING_ID')}}', 'auto');
    ga('send', 'pageview');
    </script>
    @endif
</head>
<body>
    <nav id="navbar" class="navbar is-light">
        <div class="container">
            @if ($user ?? '')
            <div class="navbar-brand">
                <a class="navbar-item has-text-weight-bold has-text-centered" href="/">
                    Unofficial RSS Feeds<br>for Stitcher Premium
                </a>

                <div id="navbarBurger" class="navbar-burger burger" data-target="menu-touch">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            @else
            <div class="navbar-brand logged-out-brand has-text-centered">
                <a class="navbar-item has-text-weight-bold" href="/">
                    Unofficial RSS Feeds<br>for Stitcher Premium
                </a>
            </div>
            @endif

            <!-- Mobile -->
            @if ($user)
            <div id="menu-touch" class="navbar-menu">
                <div class="navbar-start is-hidden-desktop">
                    <div class="navbar-item">
                        Stitcher User ID:
                        {{$user->stitcher_id}}
                    </div>
                    <div class="navbar-item is-light">
                        Subscription Expiration:
                        {{$user->expiration->format('Y-m-d')}}
                    </div>
                    <hr class="navbar-divider">
                    <a class="navbar-item" href="/logout">
                        Logout
                    </a>
                </div>
            </div>
            @endif

            <!-- Desktop -->
            <div class="navbar-end is-hidden-touch">
                @if ($user)
                <a class="navbar-item" href="/">
                    FAQ
                </a>
                <a class="navbar-item" href="/shows">
                    Shows
                </a>
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                    User
                    </a>

                    <div class="navbar-dropdown is-right">
                        <div class="navbar-item">
                            Stitcher User ID:
                            {{$user->stitcher_id}}
                        </div>
                        <div class="navbar-item is-light">
                            Subscription Expiration:
                            {{$user->expiration->format('Y-m-d')}}
                        </div>
                        <hr class="navbar-divider">
                        <a class="navbar-item" href="/logout">
                            Logout
                        </a>
                    </div>
                </div>
                @else
                <div class="navbar-item">
                    <a class="navbar-item button is-primary" href="/login">
                        Login to Stitcher
                    </a>
                </div>
                @endif
            </div>
        </div>
    </nav>

    <section class="section">
        @yield('content')
    </section>

    <section class="section has-text-centered">
        Made with ❤ by <a href="https://128.io">John Long</a>
        <br>
        <a href="https://gitlab.com/adduc-projects/stitcher-rss">Source</a>
        |
        Hosted on <a href="https://www.vultr.com/?ref=7807955-4F">Vultr</a>
    </section>
</body>
</html>
