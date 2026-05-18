@props([
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg border font-semibold tracking-wide transition duration-200 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

    $variantClasses = [
        'primary' => 'border-transparent bg-primary-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-primary-700 hover:shadow-soft active:translate-y-0 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-neutral-950',
        'secondary' => 'border-secondary-200 bg-secondary-50 text-secondary-700 shadow-sm hover:-translate-y-0.5 hover:border-secondary-300 hover:bg-secondary-100 hover:shadow-soft active:translate-y-0 focus-visible:ring-secondary-500 dark:border-secondary-800 dark:bg-secondary-950 dark:text-secondary-100 dark:hover:bg-secondary-900 dark:focus-visible:ring-offset-neutral-950',
        'danger' => 'border-transparent bg-danger-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-danger-700 hover:shadow-soft active:translate-y-0 focus-visible:ring-danger-500 dark:focus-visible:ring-offset-neutral-950',
        'ghost' => 'border-transparent bg-transparent text-neutral-700 hover:bg-neutral-100 hover:text-neutral-950 active:bg-neutral-200 focus-visible:ring-primary-500 dark:text-neutral-200 dark:hover:bg-neutral-800 dark:hover:text-white dark:focus-visible:ring-offset-neutral-950',
    ];

    $sizeClasses = [
        'sm' => 'px-3 py-2 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
    ];

    $classes = collect([
        $baseClasses,
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ])->implode(' ');
@endphp

<button {{ $attributes->merge(['type' => 'button', 'class' => $classes]) }}>
    {{ $slot }}
</button>
