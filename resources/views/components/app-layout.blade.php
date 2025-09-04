<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-100">
    @includeIf('layouts.navigation')

    <header class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 text-white">
            {{ $header ?? '' }}
        </div>
    </header>

    <main class="py-6">
        {{ $slot }}
    </main>
</body>
</html>
