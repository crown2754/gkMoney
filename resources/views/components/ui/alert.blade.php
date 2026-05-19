@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $variantClasses = [
        'info' => [
            'container' => 'border-info-200 bg-info-50 text-info-900 dark:border-info-800 dark:bg-info-950/60 dark:text-info-100',
            'icon' => 'text-info-600 dark:text-info-400',
        ],
        'success' => [
            'container' => 'border-success-200 bg-success-50 text-success-900 dark:border-success-800 dark:bg-success-950/60 dark:text-success-100',
            'icon' => 'text-success-600 dark:text-success-400',
        ],
        'warning' => [
            'container' => 'border-warning-200 bg-warning-50 text-warning-900 dark:border-warning-800 dark:bg-warning-950/60 dark:text-warning-100',
            'icon' => 'text-warning-600 dark:text-warning-400',
        ],
        'danger' => [
            'container' => 'border-danger-200 bg-danger-50 text-danger-900 dark:border-danger-800 dark:bg-danger-950/60 dark:text-danger-100',
            'icon' => 'text-danger-600 dark:text-danger-400',
        ],
    ];

    $styles = $variantClasses[$variant] ?? $variantClasses['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    role="alert"
    {{ $attributes->merge(['class' => 'flex gap-3 rounded-xl border p-4 '.$styles['container']]) }}
>
    <div class="shrink-0 {{ $styles['icon'] }}" aria-hidden="true">
        @if ($variant === 'success')
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @elseif ($variant === 'warning')
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        @elseif ($variant === 'danger')
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @else
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        @endif
    </div>

    <div class="min-w-0 flex-1">
        @if ($title)
            <p class="mb-1 text-sm font-semibold">{{ $title }}</p>
        @endif
        <div class="text-sm leading-relaxed opacity-90">
            {{ $slot }}
        </div>
    </div>

    @if ($dismissible)
        <button
            type="button"
            class="shrink-0 rounded-lg p-1 opacity-70 transition hover:bg-black/5 hover:opacity-100 dark:hover:bg-white/10"
            @click="show = false"
            aria-label="關閉提示"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
