@props(['items' => []])

<nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm" aria-label="麵包屑">
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <span class="text-gray-600" aria-hidden="true">/</span>
        @endif
        @php
            $isLast = $loop->last;
            $hasUrl = ! empty($item['url'] ?? null);
        @endphp
        @if ($hasUrl)
            <a href="{{ $item['url'] }}" class="text-gray-400 hover:text-white">{{ $item['label'] }}</a>
        @elseif ($isLast)
            <span class="font-medium text-gray-200">{{ $item['label'] }}</span>
        @else
            <span class="text-gray-400">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
