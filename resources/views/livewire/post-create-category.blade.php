<div class="p-6">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-900" id="modal-title">Create a new category</h3>
        </div>
        <button 
            wire:click="closeModal"
            class="text-slate-400 hover:text-slate-600 transition-colors"
            aria-label="Close modal"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    @if (session()->has('category-success'))
        <div class="mb-4 text-sm text-green-600">{{ session('category-success') }}</div>
    @endif

    <!-- Form Content -->
    <div class="space-y-4">
        <div>
            <x-input-label for="name" :value="__('Category Name')" />
            <x-text-input 
                id="name" 
                wire:model.blur="name" 
                class="block mt-1 w-full" 
                type="text"
                @keydown.enter="$wire.createCategory()"
                autofocus
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="flex items-center justify-end gap-3 mt-6">
        <x-secondary-button wire:click="closeModal">Cancel</x-secondary-button>
        <x-primary-button
            wire:click="createCategory"
            wire:loading.attr="disabled"
            wire:target="createCategory"                                
        >
            <span wire:loading.remove wire:target="createCategory">Create</span>
            <span wire:loading wire:target="createCategory">Creating...</span>
        </x-primary-button>
    </div>
</div>
