<div>
    @if (session()->has('image-removed-success'))
        <div class="mb-4 text-sm text-green-600">{{ session('image-removed-success') }}</div>
    @endif

    @unless($removed)
        <div class="mt-2 max-w-32 relative">
            <img src="{{ $post->getFirstMediaUrl('posts', 'thumb-128') }}" id="preview" alt="{{ $post->title }}" class="rounded-lg shadow-sm">

            <button type="button" wire:click="removeImage" class="size-6.5 m-0 p-0 absolute top-2 right-2 border-none outline-none will-change-transform will-change-opacity text-sm hover:ring-2 hover:ring-white/50 hover:bg-slate-900/70 bg-slate-900/50 text-white rounded-full transition-shadow duration-200 ease-in">
                <svg width="26" height="26" viewBox="0 0 26 26" xmlns="http://www.w3.org/2000/svg"><path d="M11.586 13l-2.293 2.293a1 1 0 0 0 1.414 1.414L13 14.414l2.293 2.293a1 1 0 0 0 1.414-1.414L14.414 13l2.293-2.293a1 1 0 0 0-1.414-1.414L13 11.586l-2.293-2.293a1 1 0 0 0-1.414 1.414L11.586 13z" fill="currentColor" fill-rule="nonzero"></path></svg>            
                <span class="sr-only">Remove Image</span>
            </button>
        </div>        
    @endunless
</div>
