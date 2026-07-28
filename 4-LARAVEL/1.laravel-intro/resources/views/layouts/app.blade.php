<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Laravel Intro')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex min-h-screen">
        @include('layouts.sidebar')

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <h1 class="font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
            </header>

            <main class="@yield('container', 'max-w-2xl') w-full mx-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>
