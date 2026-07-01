<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AssetFlow') }} - Asset & Order Management</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased selection:bg-indigo-500 selection:text-white relative min-h-screen flex flex-col">

    {{-- Background Decorative Blob --}}
    <div class="absolute inset-x-0 top-0 -z-10 transform-gpu overflow-hidden blur-3xl" aria-hidden="true">
        <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-indigo-200 to-indigo-600 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
    </div>

    <x-navbar />

    {{-- Hero --}}
    <main class="flex-grow">
        <x-hero>
            <x-slot name="badge">
                <span class="mr-2 flex h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                Inventory & Request Management, Simplified
            </x-slot>

            <x-slot name="title">
                Track your assets. <br class="hidden sm:block" />
                <span class="bg-gradient-to-r from-indigo-600 to-violet-500 bg-clip-text text-transparent">Manage every request.</span>
            </x-slot>

            <x-slot name="description">
                Browse the catalog, submit requests, and monitor approvals in real time, with a full audit trail behind every single action.
            </x-slot>

            <x-slot name="actions">
                @guest
                    <a href="{{ route('register') }}" wire:navigate
                        class="w-full sm:w-48 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-300">
                        Create an Account
                    </a>
                    <a href="{{ route('login') }}" wire:navigate
                        class="w-full sm:w-48 inline-flex items-center justify-center rounded-xl bg-white border border-gray-200 px-8 py-3.5 text-sm font-semibold text-gray-700 shadow-sm hover:border-gray-300 hover:bg-gray-50 transition-all duration-300">
                        Sign In
                    </a>
                @else
                    <a href="{{ route('catalog') }}" wire:navigate
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 hover:bg-indigo-700 hover:-translate-y-0.5 transition-all duration-300">
                        Browse Catalog
                            <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor"   viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                @endguest
            </x-slot>
        </x-hero>

        {{-- Feature Grid --}}
        <section class="max-w-6xl mx-auto px-6 pb-32">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">

                <div class="group relative bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-xl hover:shadow-indigo-600/10 hover:border-indigo-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition-colors duration-300">
                        <svg class="h-6 w-6 text-indigo-600 group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl text-gray-900 mb-3">Live Catalog</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Browse available items with real-time stock status. Always know exactly what's on hand before you request it.
                    </p>
                </div>

                <div class="group relative bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-xl hover:shadow-indigo-600/10 hover:border-indigo-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition-colors duration-300">
                        <svg class="h-6 w-6 text-indigo-600 group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl text-gray-900 mb-3">Request & Approve</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Submit a request slip, track its status, and get instant visibility once an admin approves or rejects it.
                    </p>
                </div>

                <div class="group relative bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-xl hover:shadow-indigo-600/10 hover:border-indigo-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="h-12 w-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-6 group-hover:bg-indigo-600 transition-colors duration-300">
                        <svg class="h-6 w-6 text-indigo-600 group-hover:text-white transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl text-gray-900 mb-3">Full Audit Trail</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Every action is securely logged with who, what, and when for full accountability.
                    </p>
                </div>

            </div>
        </section>
    </main>

    {{-- Footer --}}
    <x-footer />

    @livewireScripts
</body>
</html>