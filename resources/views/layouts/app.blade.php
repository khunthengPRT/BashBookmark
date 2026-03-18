<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BashBookmark' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">

    <nav class="border-b border-gray-200 bg-white shadow-sm">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-14 items-center gap-2">
                <span class="text-lg font-bold tracking-tight text-indigo-600">BashBookmark</span>
                <span class="text-sm text-gray-400">— personal snippet manager</span>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
