<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'gkMoney') }}</title>

        <script>
            (function () {
                const key = 'gk-theme';
                const stored = localStorage.getItem(key);
                const theme = stored === 'light' || stored === 'dark'
                    ? stored
                    : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.classList.toggle('dark', theme === 'dark');
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-full font-sans antialiased">
        <div class="relative flex min-h-full flex-col items-center justify-center px-4 py-10 sm:px-6">
            <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
                <div class="absolute -left-24 top-0 h-72 w-72 rounded-full bg-primary-200/40 blur-3xl dark:bg-primary-900/30"></div>
                <div class="absolute -right-24 bottom-0 h-72 w-72 rounded-full bg-secondary-200/40 blur-3xl dark:bg-secondary-900/30"></div>
            </div>

            <div class="relative mb-8 flex w-full max-w-md items-center justify-between">
                <x-ui.logo />
                <x-ui.theme-toggle />
            </div>

            <x-ui.card variant="elevated" class="relative w-full max-w-md">
                {{ $slot }}
            </x-ui.card>
        </div>
    </body>
</html>
