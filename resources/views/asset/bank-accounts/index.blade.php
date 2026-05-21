<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('銀行帳戶管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- 側邊欄：總資產、新增/編輯表單與管理員匯率更新 -->
                <div class="space-y-8 lg:col-span-1">
                    <!-- 總資產元件 -->
                    <livewire:dashboard-asset />

                    <!-- 銀行帳戶表單 (新增 / 編輯) -->
                    <livewire:bank-account-form />

                    <!-- 管理員專屬：手動更新外幣匯率 -->
                    @if(auth()->user() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                        <livewire:exchange-rate-form />
                    @endif
                </div>

                <!-- 右側主區塊：銀行帳戶清單 -->
                <div class="lg:col-span-2">
                    <livewire:bank-account-list />
                </div>

            </div>
        </div>
    </div>
</x-app-layout>