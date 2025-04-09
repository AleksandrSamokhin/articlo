<article class="flex gap-4 border-b pb-4">
		@if ( $post->thumb )
				<img src="{{ asset($post->thumb) }}" alt="{{ $post->title }}" class="w-32 h-32 object-cover rounded">
		@endif
		<div>
				<a href={{ route('categories.show', $post->category) }}>
						<span>{{ $post->category->name }}</span>
				</a>
				<span class="text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
				<h3 class="text-lg font-semibold"><a href="{{ route('posts.show', $post ) }}" class="hover:underline">{{ $post->title }}</a></h3>
				<p class="text-gray-600">{{ $post->excerpt }}</p>
		</div>
</article>