<div>
    <!-- Display Comments -->
    <div class="mt-8 p-6 bg-white dark:bg-slate-800 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Comments ({{ $post->comments->count() }})</h2>

        @forelse ($post->comments as $comment)
            <div class="mb-4 pb-4 border-b border-slate-200 dark:border-slate-700 last:border-b-0">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $comment->user->name ?? 'Anonymous' }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">{{ $comment->created_at->diffForHumans() }}</p>
                <p class="text-slate-700 dark:text-slate-300">{{ $comment->content }}</p>
            </div>
        @empty
            <p class="text-slate-500 dark:text-slate-400">No comments yet. Be the first to comment!</p>
        @endforelse
    </div>

    @auth
        <!-- Comment Section -->
        <div class="mt-8 p-6 bg-white dark:bg-slate-800 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white mb-4">Add a Comment</h2>
            <form wire:submit="saveComment" method="POST">
                @csrf

                <div class="mt-4">
                    <x-input-label for="comment" :value="__('Comment')" />
                    <x-textarea-input wire:model="comment" id="comment" rows="6" class="block mt-1 w-full" name="comment" required />
                    <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-primary-button>{{ __('Submit Comment') }}</x-primary-button>
                </div>

            </form>
        </div>
    @endauth  
</div>
