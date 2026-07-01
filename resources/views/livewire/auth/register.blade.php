<div class="w-full max-w-md mx-auto">
    <div class="bg-white shadow-xl shadow-gray-200/50 border border-gray-200 rounded-2xl p-8 sm:p-10 transition-all">
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Create an account</h1>
            <p class="mt-2 text-sm text-gray-500">Register to browse the catalog and place requests.</p>
        </div>

        <form wire:submit="register" class="space-y-6">
            {{-- Name --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Full Name
                </label>
                <input
                    wire:model="name"
                    id="name"
                    type="text"
                    autocomplete="name"
                    placeholder="Juan Dela Cruz"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition-all focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 @error('name') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                >
                @error('name')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

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
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Password
                </label>
                <input
                    wire:model="password"
                    id="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition-all focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                >
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Confirm Password
                </label>
                <input
                    wire:model="password_confirmation"
                    id="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm transition-all focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="register"
                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/20 transition-all duration-300 hover:bg-slate-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0"
            >
                <svg
                    wire:loading
                    wire:target="register"
                    class="animate-spin h-4 w-4"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span wire:loading.remove wire:target="register">Create Account</span>
                <span wire:loading wire:target="register">Creating...</span>
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" wire:navigate class="font-semibold text-indigo-600 transition-colors hover:text-slate-700">
                Sign in
            </a>
        </p>
    </div>
</div>