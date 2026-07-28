<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Laravel Intro')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <a href="/" class="text-lg font-bold text-blue-600">Laravel Intro</a>

        <div class="flex gap-4 items-center">
            @auth
                <a href="/dashboard" class="text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Dashboard
                </a>
            @else
                <a href="/login" class="text-sm text-gray-600 hover:text-gray-900">Login</a>
                <a href="/register" class="text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Register
                </a>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6">
        @yield('content')
    </main>
</body>

</html>
