@props([
	'brandName' => config('app.name', 'AssetFlow'),
	'year' => date('Y'),
])

<footer class="mt-auto border-t border-gray-200 bg-white">
	<div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 py-8 sm:flex-row">
		<div class="flex items-center gap-2">
			<svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
				<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
			</svg>
			<span class="font-semibold text-gray-900">{{ $brandName }}</span>
		</div>
		<p class="text-sm font-medium text-gray-500">
			&copy; {{ $year }} {{ $brandName }}. All rights reserved.
		</p>
	</div>
</footer>
