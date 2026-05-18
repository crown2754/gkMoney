@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $tag = $href ? 'a' : 'button';

    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-lg border font-semibold tracking-wide transition duration-200 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

    $variantClasses = [
        'primary' => 'border-transparent bg-primary-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-primary-700 hover:shadow-professional active:translate-y-0 focus-visible:ring-primary-500 dark:focus-visible:ring-offset-neutral-950',
        'secondary' => 'border-neutral-200 bg-white text-neutral-700 shadow-sm hover:-translate-y-0.5 hover:border-neutral-300 hover:bg-neutral-50 hover:shadow-professional active:translate-y-0 focus-visible:ring-primary-500 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:hover:bg-neutral-800 dark:focus-visible:ring-offset-neutral-950',
        'ghost' => 'border-transparent bg-transparent text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-primary-500 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white dark:focus-visible:ring-offset-neutral-950',
        'outline' => 'border-neutral-300 bg-transparent text-neutral-700 hover:-translate-y-0.5 hover:border-primary-400 hover:bg-primary-50 hover:text-primary-800 hover:shadow-professional active:translate-y-0 focus-visible:ring-primary-500 dark:border-neutral-600 dark:text-neutral-200 dark:hover:border-primary-500 dark:hover:bg-primary-950/50 dark:hover:text-primary-100 dark:focus-visible:ring-offset-neutral-950',
        'success' => 'border-transparent bg-success-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-success-700 hover:shadow-professional active:translate-y-0 focus-visible:ring-success-500 dark:focus-visible:ring-offset-neutral-950',
        'danger' => 'border-transparent bg-danger-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-danger-700 hover:shadow-professional active:translate-y-0 focus-visible:ring-danger-500 dark:focus-visible:ring-offset-neutral-950',
        'warning' => 'border-transparent bg-warning-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-warning-700 hover:shadow-professional active:translate-y-0 focus-visible:ring-warning-500 dark:focus-visible:ring-offset-neutral-950',
        'info' => 'border-transparent bg-info-600 text-white shadow-sm hover:-translate-y-0.5 hover:bg-info-700 hover:shadow-professional active:translate-y-0 focus-visible:ring-info-500 dark:focus-visible:ring-offset-neutral-950',
    ];

    $sizeClasses = [
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base',
        'xl' => 'px-6 py-3.5 text-base',
    ];

    $classes = collect([
        $baseClasses,
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ])->implode(' ');

    $mergedAttributes = $attributes->merge(['class' => $classes]);

    if ($tag === 'a') {
        $mergedAttributes = $mergedAttributes->merge(['href' => $href]);
    } else {
        $mergedAttributes = $mergedAttributes->merge(['type' => $type]);
    }
@endphp

<{{ $tag }} {{ $mergedAttributes }}>
    {{ $slot }}
</{{ $tag }}>
