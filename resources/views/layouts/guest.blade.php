<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AssetFlow') }} - Authentication</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex antialiased selection:bg-indigo-500 selection:text-white relative overflow-hidden">

    {{-- Decorative Background Glow --}}
    <div class="absolute top-0 left-0 -translate-x-1/4 -translate-y-1/4 w-[600px] h-[600px] bg-indigo-400/20 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 min-h-screen relative z-10">

        {{-- Left Side: Branding & Info --}}
        <div class="flex flex-col justify-center px-8 sm:px-16 lg:px-20 py-12">

            {{-- Description --}}
            <h2 class="text-4xl font-bold text-slate-900 mb-5 leading-tight tracking-tight">
                Track your assets. <br />
                <span class="text-indigo-500">Manage every request.</span>
            </h2>
            <p class="text-slate-500 text-lg max-w-md leading-relaxed mb-12">
                Manage your catalog, submit automated requests, and track real-time inventory with complete audit visibility.
            </p>

            {{-- Footer Links --}}
            <div class="flex items-center gap-4 mt-auto lg:mt-0">
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-white border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-sm">
                    Return to Home
                </a>
            </div>
        </div>

        {{-- Right Side: Form Slot --}}
        <div class="flex flex-col justify-center items-center px-4 sm:px-8 py-12 lg:py-0">
            {{ $slot }}
        </div>

    </div>

    @livewireScripts
</body>
</html>