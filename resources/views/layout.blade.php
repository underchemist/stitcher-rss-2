@inject('user', 'App\User')

<h1>Unofficial Feeds</h1>

@if ($user ?? '')
<ul>
    <li><a href="/shows">Shows</a></li>
    <li><a href="/">Help</a></li>
    <li><a href="/logout">Logout</a></li>
</ul>
@else
<ul>
    <li><a href="/login">Login</a></li>
</ul>
@endif


@yield('content')
