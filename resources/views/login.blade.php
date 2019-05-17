@isset($result)
    <h2>{{ $result }}</h2>
@endisset

<form method="post">
    <input placeholder="email" name="email" type="email" />
    <input placeholder="password" name="password" type="password" />
    <button type="submit">Login</button>
</form>
