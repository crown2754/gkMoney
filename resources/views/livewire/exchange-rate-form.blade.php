<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all duration-300">
    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-md font-bold text-gray-900 dark:text-white">匯率調整（管理員）</h3>
    </div>

    @if (session()->has('rate-message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('rate-message') }}
            </span>
            <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    <form wire:submit.prevent="updateRate" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- 幣別選擇 -->
            <div>
                <label for="currencyCode" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">外幣幣別</label>
                <select id="currencyCode" wire:model.live="currencyCode" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs px-3 py-2.5 transition duration-150">
                    <option value="">選擇外幣...</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->code }}">{{ $currency->code }} - {{ $currency->name }}</option>
                    @endforeach
                </select>
                @error('currencyCode') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- 兌台幣匯率 -->
            <div>
                <label for="rate" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">兌台幣匯率</label>
                <input type="number" step="0.000001" id="rate" wire:model.blur="rate" placeholder="例如：32.4500" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs px-3 py-2.5 transition duration-150">
                @error('rate') <span class="text-red-500 text-[11px] mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-xs font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 shadow-sm">
            更新匯率
        </button>
    </form>
</div>