@props([
    'href',
    'active' => false,
    'icon' => null,
])

@php
    $classes = collect([
        'group flex min-h-[44px] items-center gap-3 rounded-lg px-3 text-sm font-medium transition',
        $active
            ? 'bg-primary-50 text-primary-800 ring-1 ring-primary-200 dark:bg-primary-950/60 dark:text-primary-100 dark:ring-primary-800'
            : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-neutral-100',
    ])->implode(' ');
@endphp

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if ($icon)
        <span class="shrink-0 {{ $active ? 'text-primary-600 dark:text-primary-400' : 'text-neutral-400 group-hover:text-neutral-600 dark:group-hover:text-neutral-300' }}" aria-hidden="true">
            {!! $icon !!}
        </span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
