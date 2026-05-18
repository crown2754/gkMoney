@props([
    'value' => null,
    'tone' => 'default',
])

@php
    $toneClasses = [
        'default' => 'text-neutral-700 dark:text-neutral-200',
        'inverse' => 'text-neutral-100',
    ];

    $classes = collect([
        'block text-sm font-semibold',
        $toneClasses[$tone] ?? $toneClasses['default'],
    ])->implode(' ');
@endphp

<label {{ $attributes->merge(['class' => $classes]) }}>
    {{ $value ?? $slot }}
</label>
