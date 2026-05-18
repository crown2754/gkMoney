<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => '儀表板', 'url' => route('dashboard')],
            ['label' => '報表', 'url' => null],
        ]" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-100">
            報表
        </h1>
    </x-slot>

    <div class="mx-auto max-w-7xl">
        <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
            <div class="p-6 text-gray-300">
                報表與圖表將放在此頁，同樣以新台幣為主；日後可再擴充虛擬貨幣換算。
            </div>
        </div>
    </div>
</x-app-layout>
