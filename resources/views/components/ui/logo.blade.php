@props([
    'showText' => true,
])

<a
    href="{{ auth()->check() ? route('dashboard') : url('/') }}"
    {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}
>
    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-secondary-600 text-sm font-bold text-white shadow-professional">
        G
    </span>
    @if ($showText)
        <span class="truncate text-lg font-bold tracking-tight text-neutral-900 dark:text-white">
            {{ config('app.name', 'gkMoney') }}
        </span>
    @endif
</a>
