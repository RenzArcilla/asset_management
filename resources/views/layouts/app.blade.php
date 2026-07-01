<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AssetFlow') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    <div class="lg:flex lg:min-h-screen">
        <x-sidebar />

        <main class="flex-1 min-w-0 px-4 py-8 sm:px-6 lg:px-10 lg:py-10">
            <div class="max-w-6xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>