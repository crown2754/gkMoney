<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-sm font-medium text-gray-500 uppercase tracking-wider">今日總資產（TWD）</h2>
    <p class="mt-2 text-3xl font-bold {{ $totalTwd < 0 ? 'text-red-600' : 'text-gray-900' }}">
        $ {{ number_format($totalTwd, 2) }}
    </p>

    {{-- 預留歷史數據圖表區域 --}}
    <div class="mt-6 border-t border-gray-100 pt-4">
        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">歷史走勢</h3>
        <div class="h-48 flex items-center justify-center bg-gray-50 rounded-md text-gray-400 text-sm">
            <!-- TODO: 圖表元件 -->
            圖表區域（待實作）
        </div>
    </div>
</div>