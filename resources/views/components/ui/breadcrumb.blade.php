@props(['items' => []])

<nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm" aria-label="麵包屑">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <span class="text-neutral-400 dark:text-neutral-600" aria-hidden="true">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

        @php
            $isLast = $loop->last;
            $hasUrl = ! empty($item['url'] ?? null);
        @endphp

        @if ($hasUrl)
            <a href="{{ $item['url'] }}" class="text-neutral-500 transition hover:text-primary-600 dark:text-neutral-400 dark:hover:text-primary-400">
                {{ $item['label'] }}
            </a>
        @elseif ($isLast)
            <span class="font-semibold text-neutral-800 dark:text-neutral-100" aria-current="page">{{ $item['label'] }}</span>
        @else
            <span class="text-neutral-500 dark:text-neutral-400">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
