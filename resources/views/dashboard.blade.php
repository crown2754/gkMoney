<x-app-layout>
    <x-slot name="breadcrumb">
        <x-ui.breadcrumb :items="[
            ['label' => '儀表板', 'url' => null],
        ]" />
    </x-slot>

    <x-ui.page-header
        title="儀表板"
        description="掌握您的收支概況與新台幣（TWD）資產總覽。"
    />

    <x-ui.alert variant="info" title="歡迎使用 {{ config('app.name') }}" class="mb-6">
        UI 樣式庫已就緒。之後會在這裡顯示收支摘要、圖表與近期交易紀錄。
    </x-ui.alert>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-ui.stat-card
            label="本月收入"
            value="NT$ 0"
            trend="尚無資料"
            variant="success"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </x-slot>
        </x-ui.stat-card>

        <x-ui.stat-card
            label="本月支出"
            value="NT$ 0"
            trend="尚無資料"
            variant="danger"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </x-slot>
        </x-ui.stat-card>

        <x-ui.stat-card
            label="本月結餘"
            value="NT$ 0"
            trend="尚無資料"
            variant="primary"
            class="sm:col-span-2 lg:col-span-1"
        >
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </x-slot>
        </x-ui.stat-card>
    </div>

    <x-ui.card variant="accent">
        <x-slot name="header">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-50">快速操作</h2>
                <x-ui.badge variant="primary">預覽</x-ui.badge>
            </div>
        </x-slot>

        <p class="text-sm text-neutral-600 dark:text-neutral-300">
            使用下方按鈕預覽 UI 元件變體，後續可替換為實際記帳功能。
        </p>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-ui.button variant="primary">新增交易</x-ui.button>
            <x-ui.button variant="secondary">匯入資料</x-ui.button>
            <x-ui.button variant="outline">查看報表</x-ui.button>
            <x-ui.button variant="ghost">更多</x-ui.button>
        </div>
    </x-ui.card>
</x-app-layout>
