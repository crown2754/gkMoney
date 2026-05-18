<!DOCTYPE html>
<html lang="zh-Hant" class="antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'gkMoney') }}</title>
    {{-- CSRF Token --}}
    @csrf
    {{-- Vite --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    {{-- 暗色系偵測：根據系統或手動切換 --}}
    <script>
        // 從 localStorage 讀取主題偏好，預設跟隨系統
        const storedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const currentTheme = storedTheme || (systemTheme ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', currentTheme === 'dark');
    </script>
</head>
<body class="font-sans antialiased flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    {{-- 頂部導覽欄 --}}
    <header class="border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo / 品牌名稱 -->
                    <a href="{{ route('dashboard') }}" class="flex-shrink-0 text-xl font-bold text-indigo-600 dark:text-indigo-400">
                        gkMoney
                    </a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    <!-- 導覽選單（桌機版顯示）-->
                    <nav class="space-x-4">
                        <!-- 動態麵包屑將放在這裡 -->
                        @isset($breadcrumbs)
                            <div class="flex space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                @foreach($breadcrumbs as $breadcrumb)
                                    @if(!$breadcrumb['isLast'])
                                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ $breadcrumb['label'] }}</a>
                                        <span class="mx-1">/</span>
                                    @else
                                        <span class="font-medium">{{ $breadcrumb['label'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                        @endisset
                    </nav>
                </div>
                <div class="flex items-center">
                    <!-- 主題切換按鈕 -->
                    <button id="theme-toggle" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
                            aria-label="切換深色/淺色模式">
                        <svg id="theme-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <!-- 會由 JS 切換圖示 -->
                        </svg>
                    </button>

                    <!-- 使用者選單（桌機版）-->
                    <div class="ml-3 relative">
                        <div>
                            <button type="button"
                                    class="max-w-xs bg-white rounded-md dark:bg-gray-800 flex items-center text-sm font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700"
                                    id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="flex-shrink-0">
                                    <img class="h-8 w-8 rounded-full"
                                         src="{{ auth()->user ? auth()->user->profile_photo_url : asset('build/images/profile-placeholder.svg') }}"
                                         alt="">
                                </span>
                                <div class="ml-3">
                                    <div class="text-left">
                                        <div class="font-medium">{{ auth()->user ? auth()->user->name : '訪客' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            @auth
                                                {{ auth()->user->email }}
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                                <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        <!-- 下拉選單 -->
                        <div class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5"
                             id="user-menu">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                                <!-- 使用者資料 -->
                                <div class="px-4 pt-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ auth()->user ? auth()->user->name : '訪客' }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        @auth
                                            {{ auth()->user->email }}
                                        @endauth
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-700"></div>

                                <a href="{{ route('logout') }}"
                                   class="block px-4 py-2 text-sm text-left text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700"
                                   role="menuitem"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    登出
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 手機版漢堡選單按鈕 -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700"
                            id="mobile-menu-button"
                            aria-controls="mobile-menu" aria-expanded="false">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- 手機版側邊選單（抽屜式）-->
    <div class="fixed inset-0 z-50 hidden md:hidden" id="mobile-menu">
        <div class="flex min-h-full flex-col justify-end bg-white dark:bg-gray-800 sm:items-center sm:justify-center">
            <div class="relative w-full max-w-md mx-auto">
                <!-- 側邊選單內容 -->
                <div class="py-8 text-base leading-loose">
                    <div class="flex justify-between items-center px-6">
                        <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                            gkMoney
                        </div>
                        <button type="button"
                                class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-400"
                                id="mobile-menu-close">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 space-y-6">
                        <a href="{{ route('dashboard') }}"
                           class="-mx-3 block px-3 py-2 rounded-md text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                            儀表板
                        </a>
                        <a href="{{ route('transactions') }}"
                           class="-mx-3 block px-3 py-2 rounded-md text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                            交易
                        </a>
                        <a href="{{ route('reports') }}"
                           class="-mx-3 block px-3 py-2 rounded-md text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                            報表
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 桌機版側邊選單（固定在左側）-->
    <aside class="hidden md:block w-64 border-r border-gray-200 dark:border-gray-700">
        <div class="flex flex-col h-full px-4 pt-5">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                    gkMoney
                </a>
            </div>
            <div class="mt-10 flex-1 space-y-6">
                <a href="{{ route('dashboard') }}"
                   class="-mx-3 block px-3 py-2 rounded-md text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    儀表板
                </a>
                <a href="{{ route('transactions') }}"
                   class="-mx-3 block px-3 py-2 rounded-md text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    交易
                </a>
                <a href="{{ route('reports') }}"
                   class="-mx-3 block px-3 py-2 rounded-md text-base font-medium text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                    報表
                </a>
            </div>
        </div>
    </aside>

    <!-- 主要內容區 -->
    <main class="flex-1 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ now()->year }} gkMoney. 保留所有權利。
            </span>
        </div>
    </footer>

    <script>
        // 主題切換邏輯
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const htmlElement = document.documentElement;

        function updateThemeIcon(isDark) {
            themeIcon.innerHTML = isDark
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
        }

        // 初始化圖示
        const isDark = htmlElement.classList.contains('dark');
        updateThemeIcon(isDark);

        themeToggleBtn.addEventListener('click', () => {
            const isDarkNow = htmlElement.classList.toggle('dark');
            localStorage.setItem('theme', isDarkNow ? 'dark' : 'light');
            updateThemeIcon(isDarkNow);
        });

        // 手機選單開關
        const mobileMenuBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuClose = document.getElementById('mobile-menu-close');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('hidden');
        });

        mobileMenuClose.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });

        // 點擊遮罩關閉選單（可選）
        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>