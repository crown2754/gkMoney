<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-all duration-300">
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            {{ $accountId ? '編輯銀行帳戶' : '新增銀行帳戶' }}
        </h3>
        @if($accountId)
            <button type="button" wire:click="resetFields" class="text-xs text-gray-500 hover:text-red-500 transition-colors">
                清除編輯
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mb-4 p-4 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('message') }}
            </span>
            <button @click="show = false" class="text-green-500 hover:text-green-700">&times;</button>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-5">
        <!-- 銀行選擇 -->
        <div>
            <label for="bankId" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">銀行</label>
            <div class="relative">
                <select id="bankId" wire:model.live="bankId" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-4 pr-10 py-3 transition duration-150">
                    <option value="">請選擇銀行...</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->code }})</option>
                    @endforeach
                </select>
            </div>
            @error('bankId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 幣別選擇 -->
        <div>
            <label for="currencyId" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">幣別</label>
            <select id="currencyId" wire:model.live="currencyId" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pl-4 pr-10 py-3 transition duration-150">
                <option value="">請選擇幣別...</option>
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                @endforeach
            </select>
            @error('currencyId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 帳戶別名 -->
        <div>
            <label for="alias" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">帳戶別名</label>
            <input type="text" id="alias" wire:model.blur="alias" placeholder="例如：薪轉戶、主要台幣儲蓄" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 transition duration-150">
            @error('alias') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 帳號 -->
        <div>
            <label for="accountNumber" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">帳號</label>
            <input type="text" id="accountNumber" wire:model.blur="accountNumber" placeholder="請輸入銀行帳號" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 transition duration-150">
            @error('accountNumber') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 帳戶餘額 -->
        <div>
            <label for="balance" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">帳戶餘額 (可輸入負值)</label>
            <div class="relative rounded-xl shadow-sm">
                <input type="number" step="0.01" id="balance" wire:model.blur="balance" placeholder="0.00" class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-4 py-3 transition duration-150">
            </div>
            @error('balance') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <!-- 控制區 -->
        <div class="pt-2 flex gap-3">
            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-sm font-semibold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 shadow-sm shadow-indigo-200 dark:shadow-none">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $accountId ? '儲存更新' : '確認新增' }}
            </button>
            @if($accountId)
                <button type="button" wire:click="resetFields" class="w-1/2 inline-flex justify-center items-center px-4 py-3 border border-gray-200 dark:border-gray-700 text-sm font-semibold rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                    取消
                </button>
            @endif
        </div>
    </form>
</div>