<article class="bg-white shadow-slate-900/10 shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 w-full">
    <div class="p-4 flex flex-col">
        {{-- Author and Date Header --}}
        <div class="flex items-center space-x-3 mb-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('users.show', $post->user) }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                    <x-users.user-avatar :user="$post->user" />
                    <span class="font-semibold text-slate-800">{{ $post->user->name }}</span>
                </a>                
            </div>
            <span class="flex items-center gap-1 text-xs text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                {{ $post->created_at->diffForHumans() }}
            </span>
        </div>

        {{-- Post Content --}}
        <div class="mb-3">
            <h3 class="text-lg font-semibold mb-2">
                <a href="{{ route('posts.show', $post) }}" class="hover:underline text-slate-800">{{ $post->title }}</a>
            </h3>
            <p class="text-sm text-slate-600 leading-relaxed">{{ $post->excerpt }}</p>
        </div>

        {{-- Post Image --}}
        @if($post->getFirstMediaUrl('posts', 'thumb-800'))
            <a href="{{ route('posts.show', $post) }}" class="block overflow-hidden rounded-lg mt-2">
                <img src="{{ $post->getFirstMediaUrl('posts', 'thumb-800') }}" alt="{{ $post->title }}" class="w-full object-cover transition-transform duration-300 hover:scale-[1.02]">
            </a>
        @endif
    </div>
</article>