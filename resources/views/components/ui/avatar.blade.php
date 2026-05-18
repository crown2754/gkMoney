@props([
    'name' => '',
    'src' => null,
    'size' => 'md',
])

@php
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    $sizeClasses = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-12 w-12 text-base',
        'xl' => 'h-14 w-14 text-lg',
    ];

    $classes = collect([
        'inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary-100 font-semibold text-primary-700 ring-2 ring-white dark:bg-primary-950 dark:text-primary-200 dark:ring-neutral-900',
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ])->implode(' ');
@endphp

@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $name }}"
        {{ $attributes->merge(['class' => $classes.' object-cover']) }}
    >
@else
    <span {{ $attributes->merge(['class' => $classes]) }} aria-hidden="true">
        {{ $initials ?: '?' }}
    </span>
@endif
