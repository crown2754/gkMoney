<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date"
                   wire:model.live="date"
                   id="date"
                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                   :class="{ 'border-red-600 ring-red-600': $errors->has('date') }">
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
            <div class="relative">
                <span class="absolute left-0 top-0 flex h-10 items-center px-3 text-gray-500">{{ $currency }}</span>
                <input type="number"
                       wire:model.live="amount_cents"
                       id="amount"
                       min="1"
                       class="block w-full pl-10 pr-3 rounded-md border-0 py-1.5 text-right text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                       :class="{ 'border-red-600 ring-red-600': $errors->has('amount_cents') }"
                       placeholder="0.00">
            </div>
            @error('amount_cents')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <div class="relative">
                <x-dropdown align="right" width="48">
                    <div>
                        <button type="button"
                                class="flex w-full items-center justify-between rounded-md border-0 py-1.5 pl-3 pr-8 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                :class="{ 'border-red-600 ring-red-600': $errors->has('category_id') }">
                            <span>{{ $selectedCategory ? $selectedCategory->name : 'Select a category' }}</span>
                            <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 1 0 111.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-1 pt-1 pb-2">
                        <div class="flex items-center px-2 pt-2 pb-1">
                            <input type="text"
                                   wire:model.live="search"
                                   class="block w-full rounded-md border-0 py-1.5 pl-2 pr-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                   placeholder="Search categories">
                        </div>
                        <div class="block max-h-60 overflow-y-auto">
                            @forelse($filteredCategories as $category)
                                <button wire:click.prevent="selectCategory({{ $category->id }})"
                                        class="flex w-full items-center px-2 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-100"
                                        :class="{ 'bg-indigo-50': $category->id == $category_id }">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} mr-3 h-5 w-5 text-indigo-600"></i>
                                    @endif
                                    <span>{{ $category->name }}</span>
                                </button>
                            @empty
                                <p class="px-2 py-2 text-sm text-gray-500">No categories found</p>
                            @endelse
                        </div>
                    </div>
                </x-dropdown>
            </div>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
            <textarea wire:model.live="description"
                      id="description"
                      rows="3"
                      class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                      :class="{ 'border-red-600 ring-red-600': $errors->has('description') }"
                      placeholder="Add a note..."></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="receipt" class="block text-sm font-medium text-gray-700 mb-1">Attach receipt (optional)</label>
            <div class="flex items-center space-x-3">
                <button type="button"
                        wire:click="$refs.receipt.click()"
                        class="flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Upload
                </button>
                <input type="file"
                       wire:click=""
                       ref="receipt"
                       class="sr-only"
                       accept="image/*"
                       @change="uploadReceipt">
                @if($receiptPreview)
                    <div class="mt-2 flex items-center space-x-2">
                        <img src="{{ $receiptPreview }}"
                             alt="Preview"
                             class="h-10 w-10 object-cover rounded">
                        <button type="button"
                                wire:click="removeReceipt"
                                class="text-xs text-red-600 hover:text-red-800">Remove</button>
                    </div>
                @endif
            </div>
            @error('receipt_image_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="button"
                    wire:click="$emit('closeModal')"
                    class="flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-400 hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Cancel
            </button>
            <button type="submit"
                    class="flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    :disabled="getSaveButtonState()">
                @if($isSaving)
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Saving...
                @else
                    Save
                @endif
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function uploadReceipt(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                alert('File too large. Max size is 5MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                @this.set('receiptPreview', e.target.result);
            };
            reader.readAsDataURL(file);

            // Simulate upload - in reality you'd send to server and get URL
            setTimeout(() => {
                @this.set('receipt_image_url', 'https://example.com/receipt.jpg');
                @this.set('receiptPreview', null);
            }, 1500);
        }

        function removeReceipt() {
            @this.set('receipt_image_url', '');
            @this.set('receiptPreview', null);
            $refs.receipt.value = '';
        }
    </script>
@endpush