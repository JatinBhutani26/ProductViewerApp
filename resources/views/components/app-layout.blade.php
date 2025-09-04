<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    {{-- Optional top nav --}}
    @includeIf('layouts.navigation')

    <header class="bg-white border-b">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            {{ $header ?? '' }}
        </div>
    </header>

    <main class="py-6">
        {{ $slot }}
    </main>
</body>
</html>
