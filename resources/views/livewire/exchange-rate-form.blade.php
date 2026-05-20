<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">編輯匯率（{{ today()->format('Y-m-d') }}）</h2>

    <form wire:submit.prevent="save" class="space-y-4">
        @foreach ($currencies as $currency)
            <div class="flex items-center gap-4">
                <label class="w-32 text-sm font-medium text-gray-700">
                    {{ $currency->code }} <span class="text-gray-400">- {{ $currency->name }}</span>
                </label>
                <input
                    type="number"
                    step="0.0001"
                    min="0.0001"
                    wire:model="rates.{{ $currency->id }}"
                    class="block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="對 TWD 匯率"
                />
                @error("rates.{$currency->id}") <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endforeach

        <div class="pt-2">
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                儲存匯率
            </button>
        </div>
    </form>
</div>