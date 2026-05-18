@props([
    'label' => null,
])

@if ($label)
    <div {{ $attributes->merge(['class' => 'relative flex items-center py-2']) }}>
        <div class="grow border-t border-neutral-200 dark:border-neutral-800"></div>
        <span class="mx-3 shrink-0 text-xs font-medium uppercase tracking-wider text-neutral-400 dark:text-neutral-500">{{ $label }}</span>
        <div class="grow border-t border-neutral-200 dark:border-neutral-800"></div>
    </div>
@else
    <hr {{ $attributes->merge(['class' => 'border-neutral-200 dark:border-neutral-800']) }}>
@endif
