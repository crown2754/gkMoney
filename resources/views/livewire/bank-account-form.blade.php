<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        {{ $bankAccountId ? '編輯帳戶' : '新增帳戶' }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-4">
        {{-- 銀行 --}}
        <div>
            <label for="bankId" class="block text-sm font-medium text-gray-700">銀行</label>
            <select
                id="bankId"
                wire:model="bankId"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">請選擇銀行</option>
                @foreach ($banks as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                @endforeach
            </select>
            @error('bankId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- 幣別 --}}
        <div>
            <label for="currencyId" class="block text-sm font-medium text-gray-700">幣別</label>
            <select
                id="currencyId"
                wire:model="currencyId"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">請選擇幣別</option>
                @foreach ($currencies as $currency)
                    <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                @endforeach
            </select>
            @error('currencyId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- 別名 --}}
        <div>
            <label for="alias" class="block text-sm font-medium text-gray-700">別名</label>
            <input
                type="text"
                id="alias"
                wire:model="alias"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="例如：薪轉戶、外幣定存"
            />
            @error('alias') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- 帳號 --}}
        <div>
            <label for="accountNumber" class="block text-sm font-medium text-gray-700">帳號</label>
            <input
                type="text"
                id="accountNumber"
                wire:model="accountNumber"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            @error('accountNumber') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- 餘額（允許負數） --}}
        <div>
            <label for="balance" class="block text-sm font-medium text-gray-700">餘額</label>
            <input
                type="number"
                id="balance"
                wire:model="balance"
                step="0.01"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
            @error('balance') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- 按鈕 --}}
        <div class="flex items-center gap-3 pt-2">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                {{ $bankAccountId ? '更新' : '新增' }}
            </button>
            @if ($bankAccountId)
                <button
                    type="button"
                    wire:click="cancel"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300"
                >
                    取消
                </button>
            @endif
        </div>
    </form>
</div>