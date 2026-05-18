<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => '儀表板', 'url' => route('dashboard')],
            ['label' => '交易', 'url' => null],
        ]" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-100">
            交易
        </h1>
    </x-slot>

    <div class="mx-auto max-w-7xl">
        <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
            <div class="p-6 text-gray-300">
                交易列表與新增表單將使用 Livewire 實作，金額預設為新台幣（TWD）。
            </div>
        </div>
    </div>
</x-app-layout>
