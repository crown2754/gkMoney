{{-- resources/views/livewire/expense-form.blade.php --}}
<div class="max-w-xl mx-auto p-4 bg-white rounded-lg shadow-md">
    @if(session('message'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => { show = false; }, 3000)"
             class="p-4 mb-4 bg-green-100 text-green-800 rounded">
            {!! session('message') !!}
        </div>
    @endif

    <form wire:submit.prevent="submit"
          class="space-y-4">
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
            <input wire:model.debounce.200ms="amount"
                   type="number"
                   step="0.01"
                   min="0"
                   id="amount"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   placeholder="0.00">
            @error('amount')
                <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <input wire:model.debounce.200ms="description"
                   type="text"
                   id="description"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                   placeholder="Enter description">
            @error('description')
                <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="expense_date" class="block text-sm font-medium text-gray-700">Expense Date</label>
            <input wire:model.debounce.200ms="expense_date"
                   type="date"
                   id="expense_date"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            @error('expense_date')
                <span class="mt-2 block text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Save Expense
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        // Optional Alpine/Livewire interaction: focus first invalid field after validation errors
        document.addEventListener('livewire:load', () => {
            Livewire.hook('message.processed', (message, component) => {
                if (message.type === 'componentError' && component.name === 'expense-form') {
                    const firstInvalid = component.el.querySelector('.is-invalid');
                    if (firstInvalid) firstInvalid.focus();
                }
            });
        });
    </script>
@endpush