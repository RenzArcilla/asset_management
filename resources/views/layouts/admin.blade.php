<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AssetFlow') }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <x-navbar />

    <main class="max-w-6xl mx-auto px-6 py-10">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>