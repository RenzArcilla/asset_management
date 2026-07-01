@props(['transparent' => false])

<header class="border-b border-gray-100 {{ $transparent ? 'bg-transparent' : 'bg-white' }}">
    <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="font-semibold text-lg">{{ config('app.name', 'AssetFlow') }}</span>
        </a>

        <div class="flex items-center gap-3">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-200">
                        Log Out
                    </button>
                </form>

                @php
                    $user = auth()->user();
                    $isAdmin = $user->hasRole('admin');
                @endphp
                
                <a href="{{ $isAdmin ? route('admin.items') : route('catalog') }}"
                   wire:navigate
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ $isAdmin ? 'Go to Admin Dashboard' : 'Browse Catalog' }}
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate
				   class="text-sm font-semibold text-gray-600 transition hover:text-indigo-600">
					Sign in
				</a>
                <a href="{{ route('register') }}" wire:navigate
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Get Started
                </a>
            @endauth
        </div>
    </nav>
</header>