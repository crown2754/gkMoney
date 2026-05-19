@props([
    'value' => null,
    'tone' => 'default',
    'required' => false,
])

@php
    $toneClasses = [
        'default' => 'text-neutral-700 dark:text-neutral-200',
        'muted' => 'text-neutral-500 dark:text-neutral-400',
        'inverse' => 'text-neutral-100',
        'error' => 'text-danger-600 dark:text-danger-400',
        'success' => 'text-success-600 dark:text-success-400',
    ];

    $classes = collect([
        'block text-sm font-semibold',
        $toneClasses[$tone] ?? $toneClasses['default'],
    ])->implode(' ');
@endphp

<label {{ $attributes->merge(['class' => $classes]) }}>
    {{ $value ?? $slot }}
    @if ($required)
        <span class="text-danger-500" aria-hidden="true">*</span>
    @endif
</label>
