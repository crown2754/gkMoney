@props([
    'label' => null,
    'description' => null,
    'disabled' => false,
])

@php
    $inputId = $attributes->get('id') ?? 'checkbox-'.uniqid();
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'flex items-start gap-3']) }}>
    <div class="flex h-6 items-center">
        <input
            type="checkbox"
            id="{{ $inputId }}"
            @disabled($disabled)
            {{ $attributes->except('class')->merge([
                'class' => 'h-4 w-4 rounded border-neutral-300 text-primary-600 shadow-sm transition focus:ring-2 focus:ring-primary-500 focus:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-50 dark:border-neutral-600 dark:bg-neutral-950 dark:focus:ring-offset-neutral-950',
            ]) }}
        >
    </div>

    @if ($label || $description)
        <div class="text-sm leading-tight">
            @if ($label)
                <label for="{{ $inputId }}" class="font-semibold text-neutral-800 dark:text-neutral-100">
                    {{ $label }}
                </label>
            @endif
            @if ($description)
                <p class="mt-0.5 text-neutral-500 dark:text-neutral-400">{{ $description }}</p>
            @endif
        </div>
    @endif
</div>
