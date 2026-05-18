<x-app-layout>
    <x-slot name="breadcrumb">
        <x-ui.breadcrumb :items="[
            ['label' => '儀表板', 'url' => route('dashboard')],
            ['label' => '個人資料', 'url' => null],
        ]" />
    </x-slot>

    <x-ui.page-header title="個人資料" description="更新您的帳號資訊與密碼設定。" />

    <div class="space-y-6">
        <x-ui.card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-ui.card>

        <x-ui.card variant="danger">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
