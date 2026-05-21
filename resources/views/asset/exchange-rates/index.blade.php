@extends('layouts.app')

@section('title', '匯率管理')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900">匯率管理</h1>

    <livewire:exchange-rate-form :key="'exchange-rate-' . now()->timestamp" />
</div>
@endsection