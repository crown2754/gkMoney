<x-app-layout>
    <x-slot name="breadcrumb">
        <x-ui.breadcrumb :items="[
            ['label' => '儀表板', 'url' => route('dashboard')],
            ['label' => '報表', 'url' => null],
        ]" />
    </x-slot>

    <x-ui.page-header
        title="報表"
        description="檢視收支趨勢與分類統計，同樣以新台幣為主。"
    />

    <x-ui.card>
        <p class="text-sm text-neutral-600 dark:text-neutral-300">
            報表與圖表將放在此頁，同樣以新台幣為主；日後可再擴充虛擬貨幣換算。
        </p>
    </x-ui.card>
</x-app-layout>
