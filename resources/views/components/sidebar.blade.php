@props([
    'brandName' => config('app.name', 'AssetFlow'),
])

@php
    $isAdmin = auth()->user()->hasRole('admin');
@endphp

<div
    x-data="{
        mobileOpen: false,
        collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        toggleCollapsed() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebar_collapsed', this.collapsed);
        }
    }"
>
    {{-- Mobile top bar --}}
    <div class="lg:hidden sticky top-0 z-40 flex items-center justify-between border-b border-gray-200 bg-white px-4 py-3">
        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="font-semibold text-gray-900">{{ $brandName }}</span>
        </a>
        <button @click="mobileOpen = !mobileOpen" class="p-2 text-gray-500 hover:text-gray-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    {{-- Overlay (mobile only) --}}
    <div
        x-show="mobileOpen"
        x-transition.opacity
        @click="mobileOpen = false"
        class="lg:hidden fixed inset-0 z-40 bg-black/40"
        style="display: none;"
    ></div>

    {{-- Sidebar --}}
    <aside
        x-bind:class="mobileOpen ? 'translate-x-0 flex' : '-translate-x-full hidden'"
        x-bind:style="collapsed ? 'width: 72px' : 'width: 256px'"
        class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 flex-col
               transform transition-all duration-200
               lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:flex lg:z-auto relative"
    >
        {{-- Brand --}}
        <div class="hidden lg:flex items-center gap-2.5 px-4 py-5 border-b border-gray-100 overflow-hidden">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 min-w-0">
                <div class="h-9 w-9 shrink-0 rounded-xl bg-indigo-600 flex items-center justify-center shadow-md shadow-indigo-600/20">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span x-show="!collapsed" x-cloak class="font-bold text-gray-900 tracking-tight truncate">{{ $brandName }}</span>
            </a>

            <button
                @click="toggleCollapsed()"
                x-show="!collapsed"
                x-cloak
                class="ml-auto shrink-0 p-1.5 rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                title="Collapse sidebar"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        {{-- Expand button when collapsed --}}
        <div x-show="collapsed" x-cloak class="hidden lg:flex justify-center py-3 border-b border-gray-100">
            <button
                @click="toggleCollapsed()"
                class="p-1.5 rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                title="Expand sidebar"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- Nav links --}}
        {{-- Added pb-24 so bottom items aren't obscured by absolute footer --}}
        <nav class="flex-1 px-3 py-4 pb-24 space-y-1 overflow-y-auto overflow-x-hidden">
            @if ($isAdmin)
                <p x-show="!collapsed" x-cloak class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Catalog</p>

                <a
                    href="{{ route('admin.items') }}"
                    wire:navigate
                    @click="mobileOpen = false"
                    title="Item Masterlist"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('admin.items') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span x-show="!collapsed" x-cloak class="truncate">Item Masterlist</span>
                </a>

                <a
                    href="{{ route('admin.items.stock') }}"
                    wire:navigate
                    @click="mobileOpen = false"
                    title="Stock Monitoring"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('admin.items.stock') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span x-show="!collapsed" x-cloak class="truncate">Stock Monitoring</span>
                </a>

                <p x-show="!collapsed" x-cloak class="px-3 pt-4 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Requests</p>

                <a
                    href="{{ route('admin.orders') }}"
                    wire:navigate
                    @click="mobileOpen = false"
                    title="Request Queue"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('admin.orders') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!collapsed" x-cloak class="truncate">Request Queue</span>
                </a>
            @else
                <p x-show="!collapsed" x-cloak class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu</p>

                <a
                    href="{{ route('catalog') }}"
                    wire:navigate
                    @click="mobileOpen = false"
                    title="Catalog"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('catalog') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span x-show="!collapsed" x-cloak class="truncate">Catalog</span>
                </a>

                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    @click="mobileOpen = false"
                    title="My Requests"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition
                        {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="!collapsed" x-cloak class="truncate">My Requests</span>
                </a>
            @endif
        </nav>

        {{-- Footer: user + logout — absolutely pinned to bottom of screen --}}
        <div class="absolute bottom-0 left-0 w-full bg-white border-t border-gray-100 p-3 z-10">
            <div class="flex items-center gap-3 px-1 mb-2 overflow-hidden">
                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-700 shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div x-show="!collapsed" x-cloak class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    title="Log Out"
                    class="w-full flex items-center gap-3 rounded-lg px-2 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-red-600 transition"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="!collapsed" x-cloak class="truncate">Log Out</span>
                </button>
            </form>
        </div>
    </aside>
</div>