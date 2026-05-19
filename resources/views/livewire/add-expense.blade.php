<div class="max-w-xl mx-auto p-4 bg-white rounded-lg shadow-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">Add Expense</h2>
            <button type="button"
                    wire:click="$redirect(route('expenses.index'), navigate=true)"
                    class="text-gray-500 hover:text-gray-700">
                ✕
            </button>
        </div>

        <!-- Amount -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <div class="flex items-baseline">
                <span class="mr-2 text-gray-600">{{ config('app.currency_symbol', '$') }}</span>
                <input type="number"
                       step="0.01"
                       wire:model.live="amount"
                       class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-primary-500"
                       placeholder="0.00"
                       autocomplete="off">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Currency -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
            <select wire:model="currency"
                    class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm">
                @foreach ([
                    'USD' => 'US Dollar',
                    'EUR' => 'Euro',
                    'GBP' => 'British Pound',
                    'JPY' => 'Japanese Yen',
                    'CAD' => 'Canadian Dollar'
                ] as $code => $label)
                    <option value="{{ $code }}" {{ old('currency', $currency) == $code ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('currency')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Date -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date"
                   wire:model="expense_date"
                   class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm">
            @error('expense_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select wire:model="category_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm">
                <option value="">Select a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                            {{ old('category_id', $category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
            <textarea wire:model="description"
                      rows="2"
                      class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm"
                      placeholder="Enter a brief description..."></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tags -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
            <div class="mt-1 flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <span
                        wire:click="removeTag({{ $tag->id }})"
                        class="inline-flex items-center px-3 py-1 bg-primary-100 text-primary-800 text-xs font-medium rounded-full hover:bg-primary-200 cursor-pointer transition">
                        {{ $tag->label }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </span>
                @endforeach
            </div>

            <input type="text"
                   wire:model.debounce.500ms="newTagSearch"
                   placeholder="Search or add tag…"
                   class="mt-2 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm">

            @if ($newTagSearch)
                <ul class="mt-1 space-y-0.5 max-h-32 overflow-auto border border-gray-300 rounded-md bg-white">
                    @foreach ($availableTags as $tag)
                        @if (str_contains(strtolower($tag->label), strtolower($newTagSearch)))
                            <li
                                wire:click="addTag({{ $tag->id }})"
                                class="px-3 py-2 cursor-hover hover:bg-gray-100">
                                {{ $tag->label }}
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif

            @error('tags.*')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Account -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account (optional)</label>
            <select wire:model="account_id"
                    class="mt-1 block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-sm">
                <option value="">Select account</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->id }}"
                            {{ old('account_id', $account_id) == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
            @error('account_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Receipt -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Attach Receipt</label>
            <div class="mt-1 flex flex-col sm:flex-row sm:items-center">
                <button type="button"
                        wire:click="openReceiptPicker"
                        class="flex items-center px-4 py-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16v-2a2 2 0 012-2h2a2 2 0 012 2v2m-4 0h.01M12 8h.01M12 12h.01M12 16h.01M4 12H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v5a2 2 0 01-2 2h-1"/>
                    </svg>
                    Attach
                </button>

                @if ($receipt_id)
                    <div class="mt-3 sm:mt-0 sm:ml-4 flex items-center space-x-2">
                        <img src="{{ route('receipt.preview', ['id' => $receipt_id]) }}"
                             alt="Receipt preview"
                             class="w-16 h-16 object-cover rounded border border-gray-300">
                        <button type="button"
                                wire:click="removeReceipt"
                                class="text-sm text-red-600 hover:text-red-800">
                            Remove
                        </button>
                    </div>
                @endif

                @error('receipt_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="pt-4">
            <button type="submit"
                    disabled={{ $isSaving }}
                    class="w-flex items-center justify-center px-4 py-2 bg-primary-600 text-white font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus-ring-primary-500 focus:ring-offset-2 transition">
                @if ($isSaving)
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Saving...
                @else
                    Save Expense
                @endif
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Alpine for tag search/creation (if needed)
    document.addEventListener('alpine:init', () => {
        Alpine.data('tagInput', () => ({
            search: '',
            tags: @json($tags->pluck('label')->toArray()),
            selectedTags: @json($tags->pluck('id')->toArray()),
            addTag(label) {
                // This is a placeholder; actual tag creation would go through Livewire
                this.selectedTags.push(label);
                this.search = '';
            }
        }));
    });
</script>
@endpush