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
    <body class="h-full font-sans antialiased">
        <div
            x-data="{ sidebarOpen: false }"
            @keydown.window.escape="sidebarOpen = false"
            class="flex min-h-full bg-neutral-50 dark:bg-neutral-950"
        >
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-neutral-900/50 backdrop-blur-sm md:hidden"
                style="display: none;"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-neutral-200 bg-white transition-transform duration-200 ease-out dark:border-neutral-800 dark:bg-neutral-900 md:static md:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                aria-label="主選單"
            >
                <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-neutral-200 px-4 dark:border-neutral-800">
                    <x-ui.logo />
                    <button
                        type="button"
                        class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-neutral-500 transition hover:bg-neutral-100 hover:text-neutral-900 md:hidden dark:hover:bg-neutral-800 dark:hover:text-white"
                        @click="sidebarOpen = false"
                        aria-label="關閉選單"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="flex flex-1 flex-col gap-1 p-3">
                    @php
                        $iconDashboard = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>';
                        $iconTransactions = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                        $iconReports = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>';
                        $iconProfile = '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>';
                    @endphp

                    <x-ui.nav-item
                        :href="route('dashboard')"
                        :active="request()->routeIs('dashboard')"
                        :icon="$iconDashboard"
                    >
                        儀表板
                    </x-ui.nav-item>

                    <x-ui.nav-item
                        :href="route('transactions')"
                        :active="request()->routeIs('transactions')"
                        :icon="$iconTransactions"
                    >
                        交易
                    </x-ui.nav-item>

                    <x-ui.nav-item
                        :href="route('reports')"
                        :active="request()->routeIs('reports')"
                        :icon="$iconReports"
                    >
                        報表
                    </x-ui.nav-item>

                    <div class="mt-auto border-t border-neutral-200 pt-3 dark:border-neutral-800">
                        <x-ui.nav-item
                            :href="route('profile.edit')"
                            :active="request()->routeIs('profile.*')"
                            :icon="$iconProfile"
                        >
                            個人資料
                        </x-ui.nav-item>
                    </div>
                </nav>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-neutral-200 bg-white/90 px-4 backdrop-blur supports-[backdrop-filter]:bg-white/75 dark:border-neutral-800 dark:bg-neutral-900/90 dark:supports-[backdrop-filter]:bg-neutral-900/75">
                    <button
                        type="button"
                        class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-neutral-600 transition hover:bg-neutral-100 hover:text-neutral-900 md:hidden dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white"
                        @click="sidebarOpen = true"
                        aria-label="開啟選單"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="min-w-0 flex-1">
                        @isset($breadcrumb)
                            {{ $breadcrumb }}
                        @endisset
                    </div>

                    <x-ui.theme-toggle />

                    <x-ui.dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button
                                type="button"
                                class="inline-flex max-w-[12rem] items-center gap-2 rounded-lg border border-neutral-200 bg-white px-2 py-1.5 text-sm font-medium text-neutral-700 shadow-sm transition hover:border-neutral-300 hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:border-neutral-600 dark:hover:bg-neutral-800"
                            >
                                <x-ui.avatar :name="Auth::user()->name" size="sm" />
                                <span class="truncate">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 shrink-0 text-neutral-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                            </button>
                        </x-slot>

                        <x-ui.dropdown-item :href="route('profile.edit')">
                            個人資料
                        </x-ui.dropdown-item>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-ui.dropdown-item
                                :href="route('logout')"
                                danger
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                登出
                            </x-ui.dropdown-item>
                        </form>
                    </x-ui.dropdown>
                </header>

                @isset($header)
                    <div class="border-b border-neutral-200 bg-white px-4 py-5 sm:px-6 lg:px-8 dark:border-neutral-800 dark:bg-neutral-900">
                        {{ $header }}
                    </div>
                @endisset

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="mx-auto max-w-7xl">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
