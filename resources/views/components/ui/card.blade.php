@props([
    'variant' => 'default',
    'padding' => true,
])

@php
    $baseClasses = 'rounded-xl border border-neutral-200 bg-white shadow-soft dark:border-neutral-800 dark:bg-neutral-900';

    $variantClasses = [
        'default' => '',
        'elevated' => 'shadow-card hover:shadow-elevated transition-shadow duration-200',
        'interactive' => 'transition duration-200 hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-professional dark:hover:border-primary-800',
        'outlined' => 'border-2 border-neutral-300 dark:border-neutral-700',
        'accent' => 'border-l-4 border-l-primary-500',
        'success' => 'border-l-4 border-l-success-500',
        'danger' => 'border-l-4 border-l-danger-500',
        'warning' => 'border-l-4 border-l-warning-500',
        'info' => 'border-l-4 border-l-info-500',
    ];

    $paddingClass = $padding ? 'p-6' : 'p-0';

    $classes = collect([
        $baseClasses,
        $variantClasses[$variant] ?? $variantClasses['default'],
        $paddingClass,
    ])->implode(' ');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="mb-4 border-b border-neutral-100 pb-4 dark:border-neutral-800">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 border-t border-neutral-100 pt-4 dark:border-neutral-800">
            {{ $footer }}
        </div>
    @endisset
</div>
