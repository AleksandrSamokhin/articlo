<article class="flex flex-col bg-white shadow-md rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 w-full">
    @if($post->getFirstMediaUrl('posts', 'thumb-564'))
        <a href="{{ route('posts.show', $post) }}" class="block overflow-hidden">
            <img src="{{ $post->getFirstMediaUrl('posts', 'thumb-564') }}" alt="{{ $post->title }}" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
        </a>
        {{-- <img src="{{ Storage::disk('s3')->temporaryUrl($post->thumb, now()->addMinutes(2)) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover"> --}}
    @endif
    <div class="p-4 flex flex-col justify-between">
        <a href={{ route('categories.show', $post->category) }} class="self-start text-sm font-medium text-blue-500 hover:underline">
            <span>{{ $post->category->name }}</span>
        </a>
        <span class="text-xs text-gray-400 mt-1">{{ $post->created_at->diffForHumans() }}</span>
        <h3 class="text-lg font-semibold mt-2">
            <a href="{{ route('posts.show', $post ) }}" class="hover:underline text-gray-800">{{ $post->title }}</a>
        </h3>
        <p class="text-sm text-gray-600 mt-2">{{ $post->excerpt }}</p>
    </div>
</article>