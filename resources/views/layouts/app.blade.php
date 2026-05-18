<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'gkMoney') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full bg-gray-950 text-gray-100">
        <div
            x-data="{ sidebarOpen: false }"
            @keydown.window.escape="sidebarOpen = false"
            class="min-h-full flex"
        >
            {{-- 手機：側欄開啟時的遮罩 --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-black/60 md:hidden"
                style="display: none;"
                @click="sidebarOpen = false"
                aria-hidden="true"
            ></div>

            {{-- 側邊選單 --}}
            <aside
                class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-800 bg-gray-900 transition-transform duration-200 ease-out md:static md:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                aria-label="主選單"
            >
                <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-gray-800 px-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-lg font-semibold text-white">
                        <span class="truncate">{{ config('app.name') }}</span>
                    </a>
                    <button
                        type="button"
                        class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white md:hidden"
                        @click="sidebarOpen = false"
                        aria-label="關閉選單"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="flex flex-1 flex-col gap-1 p-3">
                    <a
                        href="{{ route('dashboard') }}"
                        class="flex min-h-[44px] items-center rounded-lg px-3 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800/80 hover:text-white' }}"
                    >
                        儀表板
                    </a>
                    <a
                        href="{{ route('transactions') }}"
                        class="flex min-h-[44px] items-center rounded-lg px-3 text-sm font-medium transition {{ request()->routeIs('transactions') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800/80 hover:text-white' }}"
                    >
                        交易
                    </a>
                    <a
                        href="{{ route('reports') }}"
                        class="flex min-h-[44px] items-center rounded-lg px-3 text-sm font-medium transition {{ request()->routeIs('reports') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800/80 hover:text-white' }}"
                    >
                        報表
                    </a>

                    <div class="mt-auto border-t border-gray-800 pt-3">
                        <a
                            href="{{ route('profile.edit') }}"
                            class="flex min-h-[44px] items-center rounded-lg px-3 text-sm font-medium text-gray-300 hover:bg-gray-800/80 hover:text-white"
                        >
                            個人資料
                        </a>
                    </div>
                </nav>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                {{-- 頂部列：漢堡、麵包屑、使用者 --}}
                <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-3 border-b border-gray-800 bg-gray-900/95 px-4 backdrop-blur supports-[backdrop-filter]:bg-gray-900/80">
                    <button
                        type="button"
                        class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white md:hidden"
                        @click="sidebarOpen = true"
                        aria-label="開啟選單"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <div class="min-w-0 flex-1 text-sm text-gray-400">
                        @isset($breadcrumb)
                            {{ $breadcrumb }}
                        @else
                            <span class="text-gray-500">—</span>
                        @endisset
                    </div>

                    <div class="shrink-0">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    type="button"
                                    class="inline-flex max-w-[12rem] items-center gap-2 rounded-lg px-2 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800 focus:outline-none"
                                >
                                    <span class="truncate">{{ Auth::user()->name }}</span>
                                    <svg class="h-4 w-4 shrink-0 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    個人資料
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        登出
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                @isset($header)
                    <div class="border-b border-gray-800 bg-gray-900 px-4 py-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                @endisset

                <main class="flex-1 bg-gray-950 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
