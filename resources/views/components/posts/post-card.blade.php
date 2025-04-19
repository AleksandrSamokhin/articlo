<article>
		@if($post->getFirstMediaUrl('posts', 'thumb-128'))
				<img src="{{ $post->getFirstMediaUrl('posts', 'thumb-128') }}" alt="{{ $post->title }}" class="w-32 h-32 object-cover rounded">
				{{-- <img src="{{ Storage::disk('s3')->temporaryUrl($post->thumb, now()->addMinutes(2)) }}" alt="{{ $post->title }}" class="w-32 h-32 object-cover rounded"> --}}
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