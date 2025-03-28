@extends('layouts.blog')
 
@section('content')
<main class="container mx-auto mt-6 flex gap-6">
    <!-- Blog Posts Section -->
    <section class="w-3/4 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Latest Posts</h2>
        <div class="space-y-6">
            @foreach ($posts as $post)
                <article class="flex gap-4 border-b pb-4">
                    @if ( $post->featured_image ) 
                        <img src="{{ asset('storage/' . $post->featured_image ) }}" alt="Post Image" class="w-32 h-32 object-cover rounded">
                    @endif
                    <div>
                        <a href="/?category_id={{ $post->category->id }}">
                            <span>{{ $post->category->name }}</span>
                        </a>
                        <span class="text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                        <h3 class="text-lg font-semibold"><a href="{{ route('posts.show', $post ) }}" class="hover:underline">{{ $post->title }}</a></h3>
                        <p class="text-gray-600">{{ $post->excerpt }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        {{ $posts->links() }}
    </section>
    <!-- Sidebar Section -->
    <aside class="w-1/4 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Categories</h2>
        <ul class="space-y-2">
			@foreach ($categories as $category)
                <li><a href="/?category_id={{ $category->id }}" class="text-gray-600 hover:text-gray-800">{{ $category->name }}</a></li>							
			@endforeach
        </ul>
    </aside>
</main>
@endsection