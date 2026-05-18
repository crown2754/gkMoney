@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center rounded-full border font-semibold';

    $variantClasses = [
        'neutral' => 'border-neutral-200 bg-neutral-100 text-neutral-700 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200',
        'primary' => 'border-primary-200 bg-primary-50 text-primary-700 dark:border-primary-800 dark:bg-primary-950 dark:text-primary-200',
        'success' => 'border-success-200 bg-success-50 text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-200',
        'danger' => 'border-danger-200 bg-danger-50 text-danger-700 dark:border-danger-800 dark:bg-danger-950 dark:text-danger-200',
        'warning' => 'border-warning-200 bg-warning-50 text-warning-800 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-200',
        'info' => 'border-info-200 bg-info-50 text-info-700 dark:border-info-800 dark:bg-info-950 dark:text-info-200',
        'outline' => 'border-neutral-300 bg-transparent text-neutral-600 dark:border-neutral-600 dark:text-neutral-300',
    ];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-2xs',
        'md' => 'px-2.5 py-0.5 text-xs',
        'lg' => 'px-3 py-1 text-sm',
    ];

    $classes = collect([
        $baseClasses,
        $variantClasses[$variant] ?? $variantClasses['neutral'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ])->implode(' ');
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
