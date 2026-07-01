@props([
	'brandName' => config('app.name', 'AssetFlow'),
	'homeUrl' => url('/'),
])

<header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 transition-all backdrop-blur-md">
	<nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
		<a href="{{ $homeUrl }}" wire:navigate class="group flex items-center gap-3">
			<div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 shadow-md shadow-indigo-600/20 transition group-hover:bg-indigo-700">
				<svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
					<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
				</svg>
			</div>
			<span class="text-xl font-bold tracking-tight text-gray-900 transition group-hover:text-indigo-600">{{ $brandName }}</span>
		</a>

		<div class="flex items-center gap-4">
      @auth
          <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
            wire:navigate
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 transition-all duration-300 hover:bg-slate-700 hover:shadow-lg hover:-translate-y-0.5">
              Go to Dashboard
          </a>
      @else
				<a href="{{ route('login') }}" wire:navigate
				   class="text-sm font-semibold text-gray-600 transition hover:text-indigo-600">
					Sign in
				</a>
        <a href="{{ route('register') }}" wire:navigate
          class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Get Started
        </a>
			@endauth
		</div>
	</nav>
</header>
