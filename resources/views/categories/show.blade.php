@extends('layouts.blog')
 
@section('content')
<main class="container mx-auto mt-6 flex gap-6">

    <!-- Posts -->
    <section class="w-3/4 bg-white p-6 shadow-md rounded-lg">
        
        @if ($posts->isNotEmpty())
            <div>
                <h1 class="text-xl font-semibold mb-4">Posts in {{ $category->name }}</h1>
                <div class="space-y-6 mb-4">
                    @forelse ($posts as $post)
                        <x-posts.post-card :post="$post" />
                    @endforeach
                </div>
        
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center">
                <p class="text-gray-500">No posts available.</p>
            </div>
        @endif
    </section>

		<x-sidebar :categories="$categories" />

</main>
@endsection