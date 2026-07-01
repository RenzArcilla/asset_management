<div class="w-full max-w-md mx-auto">
    <div class="bg-white shadow-xl shadow-gray-200/50 border border-gray-200 rounded-2xl p-8 sm:p-10 transition-all">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Welcome back</h1>
            <p class="mt-2 text-sm text-gray-500">Sign in to view the catalog and manage your requests.</p>
        </div>

        <form wire:submit="login" class="space-y-6">
            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email Address
                </label>
                <input
                    wire:model="email"
                    id="email"
                    type="email"
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition-all focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 @error('email') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                >
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">
                        Password
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" wire:navigate
                           class="text-xs font-semibold text-indigo-600 transition-colors hover:text-slate-700">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition-all focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                >
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center">
                <input
                    wire:model="remember"
                    id="remember"
                    type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-colors cursor-pointer"
                >
                <label for="remember" class="ml-2 block text-sm font-medium text-gray-600 cursor-pointer">
                    Remember me
                </label>
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="login"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 transition-all duration-300 hover:bg-slate-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0"
            >
                <svg
                    wire:loading
                    wire:target="login"
                    class="animate-spin h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="login">Sign In</span>
                <span wire:loading wire:target="login">Signing in...</span>
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('register') }}" wire:navigate class="font-semibold text-indigo-600 transition-colors hover:text-slate-700">
                Create one
            </a>
        </p>
    </div>
</div>