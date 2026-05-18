@props([
    'label',
    'value',
    'trend' => null,
    'variant' => 'neutral',
])

@php
    $accentClasses = [
        'neutral' => 'text-neutral-600 dark:text-neutral-400',
        'primary' => 'text-primary-600 dark:text-primary-400',
        'success' => 'text-success-600 dark:text-success-400',
        'danger' => 'text-danger-600 dark:text-danger-400',
        'warning' => 'text-warning-600 dark:text-warning-400',
        'info' => 'text-info-600 dark:text-info-400',
    ];

    $accent = $accentClasses[$variant] ?? $accentClasses['neutral'];
@endphp

<x-ui.card variant="elevated" {{ $attributes }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50">{{ $value }}</p>
            @if ($trend)
                <p class="mt-1 text-xs font-medium {{ $accent }}">{{ $trend }}</p>
            @endif
        </div>
        @isset($icon)
            <div class="rounded-xl bg-neutral-100 p-2.5 dark:bg-neutral-800 {{ $accent }}" aria-hidden="true">
                {{ $icon }}
            </div>
        @endisset
    </div>
</x-ui.card>
