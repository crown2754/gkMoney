@extends('layouts.app')

@section('title', '銀行帳戶')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">銀行帳戶管理</h1>

    {{-- 新增 / 編輯表單 --}}
    <livewire:bank-account-form :key="'form-' . ($editingId ?? 'new')" />

    {{-- 帳戶列表 --}}
    <livewire:bank-account-list :key="'list-' . now()->timestamp" />
</div>

@script
<script>
    // 監聽列表的編輯事件，觸發表單元件的 edit 方法
    Livewire.on('editBankAccount', (event) => {
        const component = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
        // 透過 dispatch 通知 BankAccountForm 載入資料
        Livewire.dispatch('loadBankAccount', { account: event.account });
    });
</script>
@endscript
@endsection