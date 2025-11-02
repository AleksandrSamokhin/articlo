<div>
    <div class="mb-4">
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" class="block mt-1 w-full" type="text" wire:model="title" required />
    </div>

    <div class="mb-4">
        <x-input-label for="content" :value="__('Content')" />
        <x-textarea-input id="content" rows="6" class="block mt-1 w-full" wire:model="content" required />
    </div>

    <div wire:loading wire:target="generateContent" class="mb-2 text-blue-500">
        Generating content, please wait...
    </div>

    @if($error)
        <div class="text-red-500 mb-2">{{ $error }}</div>
    @endif

    <button type="button" wire:click="generateContent" class="underline">
        Ask AI to generate the content
    </button>
</div>
