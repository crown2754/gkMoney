@props([
    'disabled' => false,
    'error' => false,
])

@php
    $hasError = filled($error);

    $classes = collect([
        'block w-full rounded-md border bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm transition duration-200 placeholder:text-neutral-400 focus:outline-none focus:ring-2 focus:ring-offset-0 disabled:cursor-not-allowed disabled:bg-neutral-100 disabled:text-neutral-500 dark:bg-neutral-950 dark:text-neutral-100 dark:placeholder:text-neutral-500',
        $hasError
            ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-500'
            : 'border-neutral-300 focus:border-primary-500 focus:ring-primary-500 dark:border-neutral-700',
    ])->implode(' ');
@endphp

<input
    @disabled($disabled)
    @if ($hasError) aria-invalid="true" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
