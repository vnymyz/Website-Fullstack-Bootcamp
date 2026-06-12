<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Laravel Intro')</title>
</head>

<body>
    {{-- navbar --}}
    <nav>
        <a href="/hello">Hello</a> |
        <a href="/hello/Budi">Hello Budi</a> |
        <a href="/posts">Posts</a>
    </nav>

    @yield('content')

</body>

</html>
