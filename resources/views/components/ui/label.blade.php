@props(['value' => null])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-neutral-700 dark:text-neutral-200']) }}>
    {{ $value ?? $slot }}
</label>
