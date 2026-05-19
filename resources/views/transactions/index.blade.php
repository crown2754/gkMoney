<x-app-layout>
    <x-slot name="breadcrumb">
        <x-ui.breadcrumb :items="[
            ['label' => '儀表板', 'url' => route('dashboard')],
            ['label' => '交易', 'url' => null],
        ]" />
    </x-slot>

    <x-ui.page-header
        title="交易"
        description="管理您的收入與支出紀錄，金額預設為新台幣（TWD）。"
    />

    <x-ui.card>
        <p class="text-sm text-neutral-600 dark:text-neutral-300">
            交易列表與新增表單將使用 Livewire 實作，金額預設為新台幣（TWD）。
        </p>
    </x-ui.card>
</x-app-layout>
