<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300">
    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">銀行帳戶列表</h3>
            <p class="text-xs text-gray-500 mt-1">管理並追蹤您的所有多幣別資產</p>
        </div>
        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
            共 {{ $accounts->count() }} 個帳戶
        </span>
    </div>

    @if (session()->has('list-message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="mx-6 mt-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm flex items-center justify-between">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('list-message') }}
            </span>
            <button @click="show = false" class="text-blue-500 hover:text-blue-700">&times;</button>
        </div>
    @endif

    @if($accounts->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h4 class="text-base font-semibold text-gray-900 dark:text-white">目前無帳戶資料</h4>
            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">請利用左側表單，開始建立您的第一個銀行帳戶。</p>
        </div>
    @else
        <!-- 桌機版表格 -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">帳戶資訊 / 別名</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">帳號</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">原始餘額</th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">折合台幣 (TWD)</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @foreach($accounts as $account)
                        <tr class="hover:bg-gray-50/55 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-300">
                                        {{ mb_substr($account->bank->name ?? '銀', 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $account->alias }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $account->bank->name ?? '未命名銀行' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <code class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-950 font-mono text-xs">
                                    {{ $account->account_number }}
                                </code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($account->balance, 2) }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">
                                    {{ $account->currency->code ?? 'TWD' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                    ${{ number_format($account->balance_twd, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center gap-2" x-data="{ confirmingDelete: false }">
                                    <button wire:click="edit({{ $account->id }})" class="p-1.5 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="編輯帳戶">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- 行內防呆刪除按鈕 -->
                                    <div class="relative inline-block">
                                        <button @click="confirmingDelete = true" x-show="!confirmingDelete" class="p-1.5 text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="刪除帳戶">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        
                                        <div x-show="confirmingDelete" @click.away="confirmingDelete = false" x-transition.opacity class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-950/50 p-1 rounded-lg border border-red-100 dark:border-red-900/30">
                                            <span class="text-[10px] text-red-600 dark:text-red-400 px-1 font-semibold">確定？</span>
                                            <button wire:click="delete({{ $account->id }})" class="px-1.5 py-0.5 text-[10px] text-white bg-red-600 hover:bg-red-700 rounded transition font-medium">是</button>
                                            <button @click="confirmingDelete = false" class="px-1.5 py-0.5 text-[10px] text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition font-medium">否</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- 行動裝置卡片清單 -->
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($accounts as $account)
                <div class="p-5 hover:bg-gray-50/55 dark:hover:bg-gray-700/30 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-9 w-9 rounded-xl bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-300 text-xs">
                                {{ mb_substr($account->bank->name ?? '銀', 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $account->alias }}</h4>
                                <span class="text-xs text-gray-500 block">{{ $account->bank->name ?? '未命名銀行' }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($account->balance_twd, 2) }}
                            </div>
                            <span class="text-xs text-gray-400">TWD</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-50 dark:border-gray-700/30">
                        <div>
                            <span class="font-mono bg-gray-100 dark:bg-gray-950 px-1.5 py-0.5 rounded">{{ $account->account_number }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                {{ number_format($account->balance, 2) }}
                            </span>
                            {{ $account->currency->code ?? 'TWD' }}
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-3 pt-2 border-t border-gray-50 dark:border-gray-700/30" x-data="{ confirmingDelete: false }">
                        <button wire:click="edit({{ $account->id }})" class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            編輯
                        </button>

                        <div class="relative inline-block">
                            <button @click="confirmingDelete = true" x-show="!confirmingDelete" class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                刪除
                            </button>
                            
                            <div x-show="confirmingDelete" @click.away="confirmingDelete = false" x-transition.opacity class="inline-flex items-center gap-1 bg-red-50 dark:bg-red-950/50 p-1 rounded-lg border border-red-100 dark:border-red-900/30">
                                <span class="text-xs text-red-600 dark:text-red-400 px-1 font-semibold">確定？</span>
                                <button wire:click="delete({{ $account->id }})" class="px-2 py-0.5 text-xs text-white bg-red-600 hover:bg-red-700 rounded transition font-medium">是</button>
                                <button @click="confirmingDelete = false" class="px-2 py-0.5 text-xs text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded transition font-medium">否</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>