@props([
    'href' => null,
    'danger' => false,
])

@php
    $classes = collect([
        'flex w-full min-h-[40px] items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition',
        $danger
            ? 'text-danger-600 hover:bg-danger-50 dark:text-danger-400 dark:hover:bg-danger-950/50'
            : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800',
    ])->implode(' ');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
