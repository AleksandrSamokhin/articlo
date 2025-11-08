<div x-data="{ isOpen: false }" @keydown.escape.window="isOpen = false">
    <button x-on:click="isOpen = true" class="flex cursor-pointer items-center justify-center p-2 rounded-md hover:bg-slate-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </button>

    <!-- Search Overlay -->
    <div 
        x-show="isOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-slate-900/50 overflow-y-auto"
        style="display: none;"
    >
        <!-- Overlay Content -->
        <div class="relative flex items-start justify-center min-h-screen p-4 text-center">
            <div 
                class="relative w-full max-w-2xl p-6 mx-auto mt-16 overflow-hidden text-left bg-white rounded-lg shadow-xl"
                @click.away="isOpen = false"
            >
                <!-- Close Button -->
                <button 
                    @click="isOpen = false"
                    class="absolute p-1 text-slate-400 transition-colors duration-200 rounded-full top-4 right-4 hover:bg-slate-100 hover:text-slate-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Search Header -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-slate-900">Search Posts</h2>
                </div>

                <!-- Search Input -->
                <div class="relative mb-6">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        x-ref="searchInput"
                        x-init="$watch('isOpen', value => { if(value) { $nextTick(() => $refs.searchInput.focus()) } })"
                        wire:model.live.debounce.250ms="search"
                        type="text"
                        placeholder="Search posts..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Search Results -->
                <div class="overflow-y-auto max-h-96">

                    @if (count($this->results) > 0)

                        <ul class="space-y-3">
                            @foreach ($this->results as $post)
                                <li wire:key="{{ $post->id }}" class="p-3 border rounded-lg hover:bg-slate-50 transition">
                                    <h3 class="text-lg font-medium">
                                        <a 
                                            href="{{ route('posts.show', $post) }}" 
                                            class="text-blue-600 hover:text-blue-800"
                                        >
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    
                                    <div class="mt-1 text-xs text-slate-500">
                                        By {{ $post->user->name ?? 'Unknown' }} • {{ $post->created_at->diffForHumans() }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $this->results->links() }}
                        </div>

                    @elseif (strlen($search) > 0)
                        <p class="text-slate-600 text-center">No results found for "{{ $search }}"</p>
                    @endif                    

                </div>
            </div>
        </div>
    </div>
</div>