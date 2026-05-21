<div class="bg-gradient-to-br from-indigo-500 via-indigo-600 to-indigo-700 dark:from-indigo-600 dark:to-indigo-800 rounded-2xl shadow-md p-6 text-white relative overflow-hidden transition-all duration-300">
    <!-- 背景光效飾面 -->
    <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-white/10 blur-xl"></div>
    <div class="absolute -left-6 -top-6 w-24 h-24 rounded-full bg-indigo-400/20 blur-lg"></div>

    <div class="relative z-10 flex flex-col justify-between h-full">
        <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-indigo-100 uppercase tracking-wider">總資產（台幣）</span>
            <div class="p-2 rounded-xl bg-white/10 backdrop-blur-sm">
                <svg class="w-5 h-5 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="text-2xl md:text-3xl font-extrabold tracking-tight">
                TWD ${{ number_format($totalAsset, 2) }}
            </div>
            <p class="text-[10px] text-indigo-200 mt-1.5 flex items-center gap-1">
                <svg class="w-3 h-3 text-emerald-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                已包含所有外幣與當日即時匯率折算
            </p>
        </div>
    </div>
</div>