@extends('layouts.blog')
 
@section('content')
<main class="container mx-auto mt-6 flex gap-6">
    <!-- Blog Posts Section -->
    <section class="w-3/4 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Latest Posts</h2>
        <div class="space-y-6 mb-4">
            @foreach ($posts as $post)
                <x-posts.post-card :post="$post" />
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