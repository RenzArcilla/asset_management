<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AssetFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased selection:bg-indigo-100 selection:text-indigo-900">

    <x-navbar />

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>