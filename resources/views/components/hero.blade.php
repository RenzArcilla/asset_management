<section class="mx-auto max-w-6xl px-6 pb-20 pt-28 text-center">
    @isset($badge)
        <span class="mb-8 inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-4 py-1.5 text-xs font-semibold text-indigo-600 shadow-sm">
            {{ $badge }}
        </span>
    @endisset

    @isset($title)
        <h1 class="mx-auto max-w-4xl text-5xl font-extrabold leading-[1.1] tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
            {{ $title }}
        </h1>
    @endisset

    @isset($description)
        <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-gray-500 sm:text-xl">
            {{ $description }}
        </p>
    @endisset

    @isset($actions)
        <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
            {{ $actions }}
        </div>
    @endisset
</section>