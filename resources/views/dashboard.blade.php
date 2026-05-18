<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :items="[
            ['label' => '儀表板', 'url' => null],
        ]" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-100">
            儀表板
        </h1>
    </x-slot>

    <div class="mx-auto max-w-7xl">
        <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-900 shadow-sm">
            <div class="p-6 text-gray-300">
                歡迎使用 {{ config('app.name') }}。之後會在這裡顯示收支摘要與新台幣（TWD）總覽。
            </div>
        </div>
    </div>
</x-app-layout>
